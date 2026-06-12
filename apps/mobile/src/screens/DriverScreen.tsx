import * as ImagePicker from 'expo-image-picker';
import * as Location from 'expo-location';
import { useState } from 'react';
import { Image, ScrollView, StyleSheet, Text, View } from 'react-native';

import { listDriverPackages, loginDriver, submitProof } from '../api/client';
import { Button, Field, Notice, Panel } from '../components/ui';
import { colors, spacing } from '../theme';
import type { DriverPackage, LoginResponse } from '../types';

export function DriverScreen() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [session, setSession] = useState<LoginResponse | null>(null);
  const [packages, setPackages] = useState<DriverPackage[]>([]);
  const [selectedPackage, setSelectedPackage] = useState<DriverPackage | null>(null);
  const [photoUri, setPhotoUri] = useState('');
  const [locationText, setLocationText] = useState('');
  const [latitude, setLatitude] = useState<number | null>(null);
  const [longitude, setLongitude] = useState<number | null>(null);
  const [note, setNote] = useState('');
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  async function refreshPackages(token = session?.token) {
    if (!token) {
      return;
    }

    setPackages(await listDriverPackages(token));
  }

  async function handleLogin() {
    setLoading(true);
    setError('');
    setMessage('');
    try {
      const login = await loginDriver(email, password);
      setSession(login);
      setPackages(await listDriverPackages(login.token));
    } catch (loginError) {
      setError(loginError instanceof Error ? loginError.message : 'Login gagal.');
    } finally {
      setLoading(false);
    }
  }

  async function pickCameraPhoto() {
    const permission = await ImagePicker.requestCameraPermissionsAsync();
    if (!permission.granted) {
      setError('Izin kamera diperlukan untuk mengambil foto bukti.');
      return;
    }

    const result = await ImagePicker.launchCameraAsync({
      allowsEditing: false,
      cameraType: ImagePicker.CameraType.back,
      quality: 0.75,
    });

    if (!result.canceled) {
      setPhotoUri(result.assets[0]?.uri ?? '');
    }
  }

  async function useCurrentLocation() {
    const permission = await Location.requestForegroundPermissionsAsync();
    if (!permission.granted) {
      setError('Izin lokasi diperlukan untuk mengirim titik pengantaran.');
      return;
    }

    const current = await Location.getCurrentPositionAsync({});
    setLatitude(current.coords.latitude);
    setLongitude(current.coords.longitude);
    setLocationText(`${current.coords.latitude.toFixed(6)}, ${current.coords.longitude.toFixed(6)}`);
  }

  async function handleSubmitProof() {
    if (!session || !selectedPackage) {
      return;
    }

    if (!photoUri || !locationText) {
      setError('Foto dan lokasi sampai wajib diisi.');
      return;
    }

    setLoading(true);
    setError('');
    setMessage('');
    try {
      await submitProof(session.token, selectedPackage.receipt, {
        photo_url: photoUri,
        delivered_at: new Date().toISOString(),
        delivered_location: locationText,
        latitude,
        longitude,
        note: note || null,
      });
      setMessage('Bukti terkirim. Paket dipindahkan ke status sampai tujuan.');
      setSelectedPackage(null);
      setPhotoUri('');
      setLocationText('');
      setLatitude(null);
      setLongitude(null);
      setNote('');
      await refreshPackages(session.token);
    } catch (proofError) {
      setError(proofError instanceof Error ? proofError.message : 'Gagal mengirim bukti.');
    } finally {
      setLoading(false);
    }
  }

  if (!session) {
    return (
      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.heading}>
          <Text style={styles.eyebrow}>Driver</Text>
          <Text style={styles.title}>Masuk untuk melihat tugas</Text>
          <Text style={styles.copy}>Driver hanya melihat paket yang sudah diassign oleh admin.</Text>
        </View>
        <Panel>
          <Field keyboardType="email-address" label="Email" onChangeText={setEmail} value={email} />
          <Field label="Password" onChangeText={setPassword} secureTextEntry value={password} />
          <Button label="Login Driver" loading={loading} onPress={handleLogin} />
        </Panel>
        {error ? <Notice tone="danger">{error}</Notice> : null}
      </ScrollView>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.content}>
      <View style={styles.heading}>
        <Text style={styles.eyebrow}>Driver</Text>
        <Text style={styles.title}>Paket ditugaskan</Text>
        <Text style={styles.copy}>{session.user.name}</Text>
      </View>

      <View style={styles.actions}>
        <Button label="Refresh" onPress={() => void refreshPackages()} variant="secondary" />
        <Button
          label="Logout"
          onPress={() => {
            setSession(null);
            setPackages([]);
            setSelectedPackage(null);
          }}
          variant="secondary"
        />
      </View>

      {message ? <Notice tone="success">{message}</Notice> : null}
      {error ? <Notice tone="danger">{error}</Notice> : null}

      {packages.length === 0 ? <Notice tone="muted">Belum ada paket aktif untuk akun driver ini.</Notice> : null}

      {packages.map((item) => (
        <Panel key={item.receipt}>
          <Text style={styles.badge}>{item.assignment_status}</Text>
          <Text style={styles.receipt}>{item.receipt}</Text>
          <Text style={styles.copy}>{item.destination}</Text>
          <Text style={styles.meta}>{item.latest_location}</Text>
          {item.admin_note ? <Text style={styles.note}>{item.admin_note}</Text> : null}
          <Button label="Kirim Bukti" onPress={() => setSelectedPackage(item)} />
        </Panel>
      ))}

      {selectedPackage ? (
        <Panel>
          <Text style={styles.sectionTitle}>Bukti untuk {selectedPackage.receipt}</Text>
          <Button label="Ambil Foto Kamera" onPress={pickCameraPhoto} variant="secondary" />
          {photoUri ? <Image source={{ uri: photoUri }} style={styles.preview} /> : null}
          <Field label="Lokasi sampai" onChangeText={setLocationText} value={locationText} />
          <Button label="Pakai Lokasi Saat Ini" onPress={useCurrentLocation} variant="secondary" />
          <Field label="Catatan" multiline onChangeText={setNote} value={note} />
          <Button label="Kirim Bukti" loading={loading} onPress={handleSubmitProof} />
          <Button label="Batal" onPress={() => setSelectedPackage(null)} variant="secondary" />
        </Panel>
      ) : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  actions: {
    flexDirection: 'row',
    gap: spacing.md,
  },
  badge: {
    color: colors.primary,
    fontSize: 12,
    fontWeight: '700',
    textTransform: 'uppercase',
  },
  content: {
    gap: spacing.lg,
    padding: spacing.lg,
  },
  copy: {
    color: colors.muted,
    fontSize: 15,
    lineHeight: 22,
  },
  eyebrow: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
  },
  heading: {
    gap: spacing.sm,
  },
  meta: {
    color: colors.muted,
    fontSize: 13,
  },
  note: {
    color: colors.text,
    fontSize: 14,
    lineHeight: 20,
  },
  preview: {
    aspectRatio: 4 / 3,
    borderRadius: 8,
    width: '100%',
  },
  receipt: {
    color: colors.text,
    fontSize: 24,
    fontWeight: '700',
  },
  sectionTitle: {
    color: colors.text,
    fontSize: 18,
    fontWeight: '700',
  },
  title: {
    color: colors.text,
    fontSize: 32,
    fontWeight: '700',
    lineHeight: 38,
  },
});

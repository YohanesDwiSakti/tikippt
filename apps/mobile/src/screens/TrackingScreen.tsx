import { useState } from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';

import { trackReceipt } from '../api/client';
import { Button, Field, Notice, Panel } from '../components/ui';
import { colors, spacing } from '../theme';
import type { TrackingResult } from '../types';

export function TrackingScreen() {
  const [receipt, setReceipt] = useState('');
  const [result, setResult] = useState<TrackingResult | null>(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  async function handleTrack() {
    if (receipt.trim() === '') {
      setError('Nomor resi wajib diisi.');
      return;
    }

    setLoading(true);
    setError('');
    try {
      setResult(await trackReceipt(receipt));
    } catch (trackError) {
      setResult(null);
      setError(trackError instanceof Error ? trackError.message : 'Resi tidak ditemukan.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <ScrollView contentContainerStyle={styles.content}>
      <View style={styles.heading}>
        <Text style={styles.eyebrow}>Customer</Text>
        <Text style={styles.title}>Cek status resi</Text>
        <Text style={styles.copy}>Masukkan nomor resi untuk melihat status terakhir dan riwayat paket.</Text>
      </View>

      <Panel>
        <Field label="Nomor resi" onChangeText={setReceipt} value={receipt} />
        <Button label="Cek Resi" loading={loading} onPress={handleTrack} />
      </Panel>

      {error ? <Notice tone="danger">{error}</Notice> : null}

      {result ? (
        <Panel>
          <View style={styles.rowBetween}>
            <Text style={styles.badge}>{result.status}</Text>
            <Text style={styles.meta}>{result.updated_at}</Text>
          </View>
          <Text style={styles.receipt}>{result.receipt}</Text>
          <View style={styles.grid}>
            <Info label="Tujuan" value={result.destination} />
            <Info label="Lokasi terakhir" value={result.latest_location} />
            <Info label="Driver" value={result.driver_name ?? '-'} />
          </View>
          <Text style={styles.sectionTitle}>Riwayat status</Text>
          {result.timeline.length > 0 ? (
            result.timeline.map((event) => (
              <View key={`${event.status}-${event.created_at}`} style={styles.timelineItem}>
                <View style={styles.dot} />
                <View style={styles.timelineText}>
                  <Text style={styles.timelineStatus}>{event.status}</Text>
                  <Text style={styles.meta}>{event.location}</Text>
                </View>
              </View>
            ))
          ) : (
            <Text style={styles.meta}>Belum ada riwayat tambahan.</Text>
          )}
        </Panel>
      ) : null}
    </ScrollView>
  );
}

function Info({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.info}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  badge: {
    backgroundColor: '#eef4ff',
    borderRadius: 999,
    color: colors.primary,
    fontSize: 12,
    fontWeight: '700',
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.sm,
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
  dot: {
    backgroundColor: colors.primary,
    borderRadius: 5,
    height: 10,
    marginTop: 4,
    width: 10,
  },
  eyebrow: {
    color: colors.muted,
    fontSize: 13,
    fontWeight: '700',
  },
  grid: {
    gap: spacing.md,
  },
  heading: {
    gap: spacing.sm,
  },
  info: {
    borderTopColor: colors.border,
    borderTopWidth: 1,
    gap: spacing.xs,
    paddingTop: spacing.md,
  },
  infoLabel: {
    color: colors.muted,
    fontSize: 12,
    fontWeight: '700',
  },
  infoValue: {
    color: colors.text,
    fontSize: 15,
    fontWeight: '700',
  },
  meta: {
    color: colors.muted,
    fontSize: 12,
  },
  receipt: {
    color: colors.text,
    fontSize: 26,
    fontWeight: '700',
  },
  rowBetween: {
    alignItems: 'center',
    flexDirection: 'row',
    gap: spacing.md,
    justifyContent: 'space-between',
  },
  sectionTitle: {
    color: colors.text,
    fontSize: 16,
    fontWeight: '700',
  },
  timelineItem: {
    flexDirection: 'row',
    gap: spacing.md,
  },
  timelineStatus: {
    color: colors.text,
    fontSize: 14,
    fontWeight: '700',
  },
  timelineText: {
    flex: 1,
    gap: spacing.xs,
  },
  title: {
    color: colors.text,
    fontSize: 32,
    fontWeight: '700',
    lineHeight: 38,
  },
});

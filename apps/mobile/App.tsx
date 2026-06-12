import { SafeAreaView, StatusBar, StyleSheet, Text, View, Pressable } from 'react-native';
import { useState } from 'react';
import { StatusBar as ExpoStatusBar } from 'expo-status-bar';

import { DriverScreen } from './src/screens/DriverScreen';
import { TrackingScreen } from './src/screens/TrackingScreen';
import { colors, spacing } from './src/theme';

type Tab = 'tracking' | 'driver';

export default function App() {
  const [tab, setTab] = useState<Tab>('tracking');

  return (
    <SafeAreaView style={styles.safeArea}>
      <ExpoStatusBar style="dark" />
      <View style={styles.header}>
        <View style={styles.brandMark}>
          <Text style={styles.brandMarkText}>TKI</Text>
        </View>
        <View>
          <Text style={styles.brand}>FINPROPPT</Text>
          <Text style={styles.brandCaption}>TIKI Denpasar</Text>
        </View>
      </View>

      <View style={styles.body}>{tab === 'tracking' ? <TrackingScreen /> : <DriverScreen />}</View>

      <View style={styles.tabBar}>
        <TabButton active={tab === 'tracking'} label="Tracking" onPress={() => setTab('tracking')} />
        <TabButton active={tab === 'driver'} label="Driver" onPress={() => setTab('driver')} />
      </View>
    </SafeAreaView>
  );
}

function TabButton({ active, label, onPress }: { active: boolean; label: string; onPress: () => void }) {
  return (
    <Pressable
      accessibilityRole="button"
      onPress={onPress}
      style={({ pressed }) => [styles.tabButton, active && styles.tabButtonActive, pressed && styles.pressed]}
    >
      <Text style={[styles.tabButtonText, active && styles.tabButtonTextActive]}>{label}</Text>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  body: {
    flex: 1,
  },
  brand: {
    color: colors.text,
    fontSize: 16,
    fontWeight: '700',
  },
  brandCaption: {
    color: colors.muted,
    fontSize: 12,
  },
  brandMark: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 8,
    height: 36,
    justifyContent: 'center',
    width: 36,
  },
  brandMarkText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },
  header: {
    alignItems: 'center',
    backgroundColor: colors.background,
    borderBottomColor: colors.border,
    borderBottomWidth: 1,
    flexDirection: 'row',
    gap: spacing.md,
    paddingBottom: spacing.md,
    paddingHorizontal: spacing.lg,
    paddingTop: (StatusBar.currentHeight ?? 0) + spacing.md,
  },
  pressed: {
    transform: [{ scale: 0.98 }],
  },
  safeArea: {
    backgroundColor: colors.background,
    flex: 1,
  },
  tabBar: {
    backgroundColor: colors.background,
    borderTopColor: colors.border,
    borderTopWidth: 1,
    flexDirection: 'row',
    gap: spacing.md,
    padding: spacing.md,
  },
  tabButton: {
    alignItems: 'center',
    backgroundColor: colors.surface,
    borderRadius: 8,
    flex: 1,
    minHeight: 48,
    justifyContent: 'center',
  },
  tabButtonActive: {
    backgroundColor: colors.primary,
  },
  tabButtonText: {
    color: colors.muted,
    fontSize: 14,
    fontWeight: '700',
  },
  tabButtonTextActive: {
    color: '#ffffff',
  },
});

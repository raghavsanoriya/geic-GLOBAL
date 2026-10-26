import { MaterialCommunityIcons } from '@expo/vector-icons';
import React, { useEffect, useMemo, useState } from 'react';
import {
  AccessibilityInfo,
  Image,
  ImageBackground,
  type ImageSourcePropType,
  Linking,
  Modal,
  Pressable,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  type TextInputProps,
  type TextProps,
  TextInput,
  useWindowDimensions,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAppState } from './app-state';
import { colors, fonts, shadows } from './theme';
import type { Destination, RootTabParamList } from './types';

type IconName = keyof typeof MaterialCommunityIcons.glyphMap;

export function AppText({ style, ...props }: TextProps) {
  return <Text {...props} style={[{ color: colors.ink, fontFamily: fonts.regular }, style]} />;
}

export function Icon({ name, size = 22, color = colors.navy }: { name: string; size?: number; color?: string }) {
  return <MaterialCommunityIcons name={name as IconName} size={size} color={color} />;
}

export function imageSource(item: { image?: ImageSourcePropType; imageUrl?: string }): ImageSourcePropType {
  return item.imageUrl ? { uri: item.imageUrl } : item.image ?? require('../assets/student-guidance.jpg');
}

export function PrimaryButton({ label, icon = 'arrow-right', onPress, disabled = false, variant = 'primary' }: {
  label: string;
  icon?: string;
  onPress: () => void;
  disabled?: boolean;
  variant?: 'primary' | 'secondary';
}) {
  return (
    <Pressable
      accessibilityRole="button"
      accessibilityLabel={label}
      accessibilityState={{ disabled }}
      disabled={disabled}
      onPress={onPress}
      style={({ pressed }) => [styles.button, variant === 'secondary' && styles.buttonSecondary, disabled && styles.disabled, pressed && styles.pressed]}
    >
      <AppText style={[styles.buttonText, variant === 'secondary' && styles.buttonTextSecondary]}>{label}</AppText>
      <Icon name={icon} size={19} color={variant === 'secondary' ? colors.red : colors.white} />
    </Pressable>
  );
}

export function SectionTitle({ eyebrow, title, copy, action, onAction }: { eyebrow?: string; title: string; copy?: string; action?: string; onAction?: () => void }) {
  return (
    <View style={styles.sectionTitleRow}>
      <View style={styles.sectionTitleCopy}>
        {eyebrow ? <AppText style={styles.eyebrow}>{eyebrow}</AppText> : null}
        <AppText accessibilityRole="header" style={styles.sectionTitle}>{title}</AppText>
        {copy ? <AppText style={styles.sectionCopy}>{copy}</AppText> : null}
      </View>
      {action && onAction ? <Pressable accessibilityRole="button" onPress={onAction} style={styles.sectionAction}><AppText style={styles.sectionActionText}>{action}</AppText></Pressable> : null}
    </View>
  );
}

const menuItems: { tab: keyof RootTabParamList; label: string; icon: string }[] = [
  { tab: 'Home', label: 'Home', icon: 'home-outline' },
  { tab: 'Explore', label: 'Explore destinations', icon: 'compass-outline' },
  { tab: 'Advisor', label: 'AI Advisor', icon: 'robot-outline' },
  { tab: 'Evaluate', label: 'Evaluate profile', icon: 'clipboard-check-outline' },
  { tab: 'More', label: 'Services and more', icon: 'view-grid-outline' },
];

export function AppHeader({ onNavigate, onNotifications, onBooking }: {
  onNavigate: (tab: keyof RootTabParamList) => void;
  onNotifications: () => void;
  onBooking: () => void;
}) {
  const [menuOpen, setMenuOpen] = useState(false);
  const { catalog, readNotificationIds } = useAppState();
  const unread = catalog.events.filter((event) => !readNotificationIds.includes(`event:${event.id}`)).length;

  return (
    <View style={styles.header}>
      <Pressable accessibilityRole="button" accessibilityLabel="Open menu" accessibilityState={{ expanded: menuOpen }} onPress={() => setMenuOpen(true)} style={({ pressed }) => [styles.iconButton, pressed && styles.pressed]}>
        <Icon name="menu" />
      </Pressable>
      <View style={styles.brand} accessible accessibilityLabel="Trans Globe by GEIC">
        <Image source={require('../assets/trans-globe-logo.png')} style={styles.logo} resizeMode="contain" />
        <View>
          <AppText style={styles.brandName}>TRANS GLOBE</AppText>
          <AppText style={styles.brandMeta}>Global education specialists</AppText>
        </View>
      </View>
      <Pressable accessibilityRole="button" accessibilityLabel={`Notifications, ${unread} unread`} onPress={onNotifications} style={({ pressed }) => [styles.iconButton, pressed && styles.pressed]}>
        <Icon name="bell-outline" />
        {unread ? <View style={styles.badge}><AppText style={styles.badgeText}>{Math.min(unread, 9)}</AppText></View> : null}
      </Pressable>

      <Modal visible={menuOpen} transparent animationType="fade" onRequestClose={() => setMenuOpen(false)}>
        <View style={styles.modalScrim}>
          <Pressable accessibilityLabel="Close menu" style={StyleSheet.absoluteFill} onPress={() => setMenuOpen(false)} />
          <SafeAreaView edges={['top', 'bottom']} style={styles.drawer}>
            <View style={styles.drawerHead}>
              <Image source={require('../assets/trans-globe-logo.png')} style={styles.drawerLogo} resizeMode="contain" />
              <Pressable accessibilityRole="button" accessibilityLabel="Close menu" onPress={() => setMenuOpen(false)} style={({ pressed }) => [styles.iconButton, pressed && styles.pressed]}><Icon name="close" /></Pressable>
            </View>
            <AppText style={styles.drawerTitle}>Your study journey</AppText>
            <AppText style={styles.drawerCopy}>Explore verified guidance and move to the next useful step.</AppText>
            <View style={styles.drawerLinks}>
              {menuItems.map((item) => (
                <Pressable key={item.tab} accessibilityRole="button" onPress={() => { setMenuOpen(false); onNavigate(item.tab); }} style={({ pressed }) => [styles.drawerLink, pressed && styles.pressed]}>
                  <View style={styles.drawerIcon}><Icon name={item.icon} color={colors.red} /></View>
                  <AppText style={styles.drawerLabel}>{item.label}</AppText>
                  <Icon name="chevron-right" color={colors.muted} />
                </Pressable>
              ))}
            </View>
            <PrimaryButton label="Book free counselling" icon="calendar-check-outline" onPress={() => { setMenuOpen(false); onBooking(); }} />
            <Pressable accessibilityRole="link" onPress={() => void Linking.openURL('https://geic.in')} style={styles.websiteLink}><AppText style={styles.websiteText}>Visit geic.in</AppText></Pressable>
          </SafeAreaView>
        </View>
      </Modal>
    </View>
  );
}

export function AppScreen({ children, header, refreshing, onRefresh, keyboardShouldPersistTaps = 'handled' }: {
  children: React.ReactNode;
  header?: React.ReactNode;
  refreshing?: boolean;
  onRefresh?: () => void;
  keyboardShouldPersistTaps?: 'always' | 'never' | 'handled';
}) {
  const { width } = useWindowDimensions();
  const gutter = width >= 768 ? 32 : 16;
  return (
    <SafeAreaView edges={['top']} style={styles.safeArea}>
      <View style={[styles.page, { paddingHorizontal: gutter }]}>
        {header}
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps={keyboardShouldPersistTaps}
          refreshControl={onRefresh ? <RefreshControl refreshing={Boolean(refreshing)} onRefresh={onRefresh} tintColor={colors.red} /> : undefined}
        >
          {children}
        </ScrollView>
      </View>
    </SafeAreaView>
  );
}

export function DetailScreenShell({ title, onBack, children }: { title: string; onBack: () => void; children: React.ReactNode }) {
  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.detailHeader}>
        <Pressable accessibilityRole="button" accessibilityLabel="Go back" onPress={onBack} style={({ pressed }) => [styles.iconButton, pressed && styles.pressed]}><Icon name="arrow-left" /></Pressable>
        <AppText accessibilityRole="header" numberOfLines={1} style={styles.detailHeaderTitle}>{title}</AppText>
        <View style={{ width: 48 }} />
      </View>
      <ScrollView contentContainerStyle={styles.detailContent} showsVerticalScrollIndicator={false}>{children}</ScrollView>
    </SafeAreaView>
  );
}

export function TextField({ label, error, multiline, ...props }: TextInputProps & { label: string; error?: string }) {
  return (
    <View style={styles.fieldWrap}>
      <AppText style={styles.fieldLabel}>{label}</AppText>
      <TextInput
        accessibilityLabel={label}
        placeholderTextColor="#7C8494"
        {...props}
        multiline={multiline}
        style={[styles.field, multiline && styles.fieldMultiline, error && styles.fieldError]}
      />
      {error ? <AppText accessibilityLiveRegion="polite" style={styles.errorText}>{error}</AppText> : null}
    </View>
  );
}

export function ChoiceChip({ label, selected, onPress }: { label: string; selected: boolean; onPress: () => void }) {
  return (
    <Pressable accessibilityRole="button" accessibilityState={{ selected }} onPress={onPress} style={({ pressed }) => [styles.chip, selected && styles.chipSelected, pressed && styles.pressed]}>
      <AppText style={[styles.chipText, selected && styles.chipTextSelected]}>{label}</AppText>
    </Pressable>
  );
}

export function InfoBanner({ text, tone = 'info' }: { text: string; tone?: 'info' | 'error' | 'success' }) {
  return <View accessibilityLiveRegion="polite" style={[styles.infoBanner, tone === 'error' && styles.infoError, tone === 'success' && styles.infoSuccess]}><Icon name={tone === 'error' ? 'alert-circle-outline' : tone === 'success' ? 'check-circle-outline' : 'information-outline'} color={tone === 'error' ? colors.danger : tone === 'success' ? colors.success : colors.navy} /><AppText style={styles.infoText}>{text}</AppText></View>;
}

export function HeroCarousel({ slides, onOpen }: { slides: Destination[]; onOpen: (id: string) => void }) {
  const [index, setIndex] = useState(0);
  const [paused, setPaused] = useState(false);
  const [reduceMotion, setReduceMotion] = useState(false);
  const slide = slides[index] ?? slides[0];

  useEffect(() => {
    AccessibilityInfo.isReduceMotionEnabled().then(setReduceMotion).catch(() => undefined);
    const subscription = AccessibilityInfo.addEventListener('reduceMotionChanged', setReduceMotion);
    return () => subscription.remove();
  }, []);

  useEffect(() => {
    if (paused || reduceMotion || slides.length < 2) return;
    const timer = setInterval(() => setIndex((current) => (current + 1) % slides.length), 5000);
    return () => clearInterval(timer);
  }, [paused, reduceMotion, slides.length]);

  const position = useMemo(() => `${index + 1} of ${slides.length}`, [index, slides.length]);
  if (!slide) return null;

  return (
    <View onTouchStart={() => setPaused(true)} style={styles.hero}>
      <ImageBackground source={imageSource(slide)} style={styles.heroImage} imageStyle={styles.heroImageStyle}>
        <View style={styles.heroShade} />
        <View style={styles.heroCopy}>
          <AppText style={styles.heroKicker}>STUDY DESTINATION</AppText>
          <AppText accessibilityRole="header" style={styles.heroTitle}>{slide.name}</AppText>
          <AppText style={styles.heroText}>{slide.tagline}</AppText>
          <Pressable accessibilityRole="button" accessibilityLabel={`Open ${slide.name} destination, slide ${position}`} onPress={() => onOpen(slide.id)} style={({ pressed }) => [styles.heroCta, pressed && styles.pressed]}>
            <AppText style={styles.heroCtaText}>Explore destination</AppText><Icon name="arrow-right" color={colors.white} size={18} />
          </Pressable>
        </View>
        <View style={styles.heroControls}>
          <Pressable accessibilityRole="button" accessibilityLabel="Previous slide" onPress={(event) => { event.stopPropagation(); setIndex((index - 1 + slides.length) % slides.length); setPaused(true); }} style={styles.heroControl}><Icon name="chevron-left" color={colors.white} /></Pressable>
          <AppText accessibilityLiveRegion="polite" style={styles.heroPosition}>{position}</AppText>
          <Pressable accessibilityRole="button" accessibilityLabel={paused ? 'Resume slides' : 'Pause slides'} accessibilityState={{ checked: paused }} onPress={(event) => { event.stopPropagation(); setPaused((value) => !value); }} style={styles.heroControl}><Icon name={paused ? 'play' : 'pause'} color={colors.white} size={18} /></Pressable>
          <Pressable accessibilityRole="button" accessibilityLabel="Next slide" onPress={(event) => { event.stopPropagation(); setIndex((index + 1) % slides.length); setPaused(true); }} style={styles.heroControl}><Icon name="chevron-right" color={colors.white} /></Pressable>
        </View>
      </ImageBackground>
    </View>
  );
}

export const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: colors.canvas },
  page: { flex: 1, width: '100%', maxWidth: 980, alignSelf: 'center' },
  scrollContent: { paddingTop: 8, paddingBottom: 120 },
  header: { minHeight: 72, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 8 },
  brand: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', minWidth: 0 },
  logo: { width: 38, height: 38, marginRight: 8 },
  brandName: { color: colors.red, fontFamily: fonts.extraBold, fontSize: 14, letterSpacing: 0.5 },
  brandMeta: { color: colors.muted, fontFamily: fonts.medium, fontSize: 8, marginTop: 1 },
  iconButton: { width: 48, height: 48, borderRadius: 16, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, alignItems: 'center', justifyContent: 'center' },
  badge: { position: 'absolute', right: 4, top: 3, minWidth: 18, height: 18, borderRadius: 9, paddingHorizontal: 4, backgroundColor: colors.red, alignItems: 'center', justifyContent: 'center', borderWidth: 2, borderColor: colors.white },
  badgeText: { color: colors.white, fontFamily: fonts.bold, fontSize: 9 },
  modalScrim: { flex: 1, backgroundColor: 'rgba(7, 15, 29, 0.58)', flexDirection: 'row' },
  drawer: { width: '88%', maxWidth: 390, height: '100%', backgroundColor: colors.canvas, paddingHorizontal: 20, paddingBottom: 24, ...shadows.card },
  drawerHead: { minHeight: 82, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  drawerLogo: { width: 92, height: 58 },
  drawerTitle: { fontFamily: fonts.extraBold, fontSize: 27, lineHeight: 33 },
  drawerCopy: { color: colors.muted, fontSize: 14, lineHeight: 21, marginTop: 8 },
  drawerLinks: { marginVertical: 24, gap: 8 },
  drawerLink: { minHeight: 58, flexDirection: 'row', alignItems: 'center', borderRadius: 16, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, paddingHorizontal: 12 },
  drawerIcon: { width: 38, height: 38, borderRadius: 12, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 12 },
  drawerLabel: { flex: 1, fontFamily: fonts.bold, fontSize: 14 },
  websiteLink: { minHeight: 48, alignItems: 'center', justifyContent: 'center', marginTop: 8 },
  websiteText: { color: colors.red, fontFamily: fonts.bold },
  sectionTitleRow: { flexDirection: 'row', alignItems: 'flex-end', marginTop: 28, marginBottom: 14, gap: 12 },
  sectionTitleCopy: { flex: 1 },
  eyebrow: { color: colors.red, fontFamily: fonts.extraBold, fontSize: 11, letterSpacing: 1.3, marginBottom: 6 },
  sectionTitle: { fontFamily: fonts.extraBold, fontSize: 23, lineHeight: 29 },
  sectionCopy: { color: colors.muted, fontSize: 13, lineHeight: 20, marginTop: 5 },
  sectionAction: { minHeight: 48, justifyContent: 'center', paddingHorizontal: 4 },
  sectionActionText: { color: colors.red, fontFamily: fonts.bold, fontSize: 13 },
  button: { minHeight: 52, borderRadius: 15, backgroundColor: colors.red, paddingHorizontal: 18, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 9 },
  buttonSecondary: { backgroundColor: colors.white, borderWidth: 1, borderColor: colors.red },
  buttonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 14 },
  buttonTextSecondary: { color: colors.red },
  disabled: { opacity: 0.48 },
  pressed: { opacity: 0.72 },
  detailHeader: { minHeight: 72, paddingHorizontal: 16, flexDirection: 'row', alignItems: 'center', gap: 8, borderBottomWidth: 1, borderBottomColor: colors.line },
  detailHeaderTitle: { flex: 1, textAlign: 'center', fontFamily: fonts.bold, fontSize: 16 },
  detailContent: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 48, width: '100%', maxWidth: 780, alignSelf: 'center' },
  fieldWrap: { marginBottom: 14 },
  fieldLabel: { fontFamily: fonts.bold, fontSize: 13, marginBottom: 7 },
  field: { minHeight: 50, borderRadius: 13, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, color: colors.ink, fontFamily: fonts.regular, fontSize: 15, paddingHorizontal: 14, paddingVertical: 12 },
  fieldMultiline: { minHeight: 104, textAlignVertical: 'top' },
  fieldError: { borderColor: colors.danger },
  errorText: { color: colors.danger, fontFamily: fonts.medium, fontSize: 12, marginTop: 5 },
  chip: { minHeight: 48, borderRadius: 24, paddingHorizontal: 15, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white },
  chipSelected: { backgroundColor: colors.navy, borderColor: colors.navy },
  chipText: { color: colors.muted, fontFamily: fonts.bold, fontSize: 12 },
  chipTextSelected: { color: colors.white },
  infoBanner: { borderRadius: 14, padding: 13, backgroundColor: colors.softBlue, flexDirection: 'row', alignItems: 'flex-start', gap: 10, marginVertical: 10 },
  infoError: { backgroundColor: '#FFF0EF' },
  infoSuccess: { backgroundColor: '#ECF8F3' },
  infoText: { flex: 1, color: colors.muted, fontSize: 13, lineHeight: 19 },
  hero: { height: 330, borderRadius: 26, overflow: 'hidden', backgroundColor: colors.navyDeep, ...shadows.card },
  heroImage: { flex: 1, justifyContent: 'flex-end' },
  heroImageStyle: { opacity: 0.92 },
  heroShade: { position: 'absolute', inset: 0, backgroundColor: 'rgba(7, 20, 42, 0.52)' },
  heroCopy: { padding: 22, paddingBottom: 72 },
  heroKicker: { color: '#FFD0D2', fontFamily: fonts.extraBold, fontSize: 11, letterSpacing: 1.3 },
  heroTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 35, lineHeight: 41, marginTop: 7 },
  heroText: { color: '#EDF2FA', fontFamily: fonts.medium, fontSize: 14, lineHeight: 21, marginTop: 7, maxWidth: 500 },
  heroCta: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 14 },
  heroCtaText: { color: colors.white, fontFamily: fonts.bold, fontSize: 13 },
  heroControls: { position: 'absolute', left: 16, right: 16, bottom: 14, minHeight: 44, flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end', gap: 7 },
  heroControl: { width: 44, height: 44, borderRadius: 22, backgroundColor: 'rgba(7, 17, 34, 0.74)', alignItems: 'center', justifyContent: 'center' },
  heroPosition: { color: colors.white, fontFamily: fonts.bold, fontSize: 12, marginHorizontal: 4 },
});

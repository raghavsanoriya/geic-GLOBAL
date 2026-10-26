import type { NativeStackScreenProps } from '@react-navigation/native-stack';
import React, { useState } from 'react';
import { ImageBackground, KeyboardAvoidingView, Platform, Pressable, StyleSheet, View } from 'react-native';
import { ApiError, submitEnquiry } from './api';
import { useAppState } from './app-state';
import { AppText, DetailScreenShell, Icon, imageSource, InfoBanner, PrimaryButton, TextField } from './components';
import { colors, fonts, shadows } from './theme';
import type { EnquiryDraft, RootStackParamList } from './types';

function DetailHero({ item, eyebrow, title, copy }: { item: { image?: Parameters<typeof imageSource>[0]['image']; imageUrl?: string }; eyebrow: string; title: string; copy: string }) {
  return (
    <ImageBackground source={imageSource(item)} style={styles.hero} imageStyle={styles.heroImage}>
      <View style={styles.heroShade} />
      <View style={styles.heroCopy}><AppText style={styles.eyebrow}>{eyebrow}</AppText><AppText accessibilityRole="header" style={styles.heroTitle}>{title}</AppText><AppText style={styles.heroText}>{copy}</AppText></View>
    </ImageBackground>
  );
}

function DataRows({ title, rows }: { title: string; rows?: string[][] }) {
  if (!rows?.length) return null;
  return <View style={styles.section}><AppText accessibilityRole="header" style={styles.sectionTitle}>{title}</AppText><View style={styles.rows}>{rows.map(([label, value]) => <View key={`${label}-${value}`} style={styles.dataRow}><AppText style={styles.dataLabel}>{label}</AppText><AppText style={styles.dataValue}>{value}</AppText></View>)}</View></View>;
}

function Bullets({ title, items }: { title: string; items?: string[] }) {
  if (!items?.length) return null;
  return <View style={styles.section}><AppText accessibilityRole="header" style={styles.sectionTitle}>{title}</AppText>{items.map((item) => <View key={item} style={styles.bullet}><Icon name="check-circle-outline" color={colors.red} size={20} /><AppText style={styles.bulletText}>{item}</AppText></View>)}</View>;
}

export function DestinationDetailScreen({ route, navigation }: NativeStackScreenProps<RootStackParamList, 'Destination'>) {
  const { catalog } = useAppState();
  const item = catalog.destinations.find((destination) => destination.id === route.params.id);
  if (!item) return <DetailScreenShell title="Destination" onBack={navigation.goBack}><InfoBanner text="This destination is not available in the current catalogue." tone="error" /></DetailScreenShell>;
  return (
    <DetailScreenShell title={item.name} onBack={navigation.goBack}>
      <DetailHero item={item} eyebrow="STUDY DESTINATION" title={item.name} copy={item.tagline} />
      <View style={styles.leadCard}><AppText style={styles.leadTitle}>Plan with verified guidance</AppText><AppText style={styles.leadText}>{item.detail}</AppText><InfoBanner text="Fees, entry criteria, work rights and immigration rules change. Confirm current details before applying." /></View>
      <Bullets title="Why students explore this option" items={item.highlights} />
      <DataRows title="At a glance" rows={item.facts} />
      <DataRows title="Planning costs" rows={item.costs} />
      <DataRows title="Common intakes" rows={item.intakes} />
      {item.universities?.length ? <View style={styles.section}><AppText accessibilityRole="header" style={styles.sectionTitle}>Universities to research</AppText><View style={styles.tagWrap}>{item.universities.slice(0, 8).map((name) => <AppText key={name} style={styles.tag}>{name}</AppText>)}</View></View> : null}
      <PrimaryButton label={`Discuss studying in ${item.name}`} icon="calendar-check-outline" onPress={() => navigation.navigate('Booking', { kind: 'counselling', referenceId: item.id, title: `Study in ${item.name}` })} />
    </DetailScreenShell>
  );
}

export function ServiceDetailScreen({ route, navigation }: NativeStackScreenProps<RootStackParamList, 'Service'>) {
  const { catalog } = useAppState();
  const item = catalog.services.find((service) => service.id === route.params.id);
  if (!item) return <DetailScreenShell title="Service" onBack={navigation.goBack}><InfoBanner text="This service is not available in the current catalogue." tone="error" /></DetailScreenShell>;
  return (
    <DetailScreenShell title={item.title} onBack={navigation.goBack}>
      <DetailHero item={item} eyebrow="GEIC SERVICE" title={item.title} copy={item.subtitle} />
      <View style={styles.leadCard}><AppText style={styles.leadTitle}>{item.description}</AppText><AppText style={styles.leadText}>{item.overview ?? item.description}</AppText></View>
      <Bullets title="What this helps you achieve" items={item.highlights} />
      {item.process?.length ? <View style={styles.section}><AppText accessibilityRole="header" style={styles.sectionTitle}>How support works</AppText>{item.process.map(([title, copy], index) => <View key={title} style={styles.processRow}><View style={styles.processNumber}><AppText style={styles.processNumberText}>{index + 1}</AppText></View><View style={{ flex: 1 }}><AppText style={styles.processTitle}>{title}</AppText><AppText style={styles.processCopy}>{copy}</AppText></View></View>)}</View> : null}
      <PrimaryButton label="Start my plan" icon="arrow-right" onPress={() => navigation.navigate('Booking', { kind: 'service', referenceId: item.id, title: item.title })} />
    </DetailScreenShell>
  );
}

export function EventDetailScreen({ route, navigation }: NativeStackScreenProps<RootStackParamList, 'Event'>) {
  const { catalog } = useAppState();
  const item = catalog.events.find((event) => event.id === route.params.id);
  if (!item) return <DetailScreenShell title="Event" onBack={navigation.goBack}><InfoBanner text="This event is not available in the current catalogue." tone="error" /></DetailScreenShell>;
  return (
    <DetailScreenShell title={item.title} onBack={navigation.goBack}>
      <DetailHero item={item} eyebrow={item.type.toUpperCase()} title={item.title} copy={item.destination ?? 'Study abroad event'} />
      <View style={styles.metaCard}><View style={styles.metaRow}><Icon name="calendar-outline" color={colors.red} /><AppText style={styles.metaText}>{item.date} · {item.time}</AppText></View><View style={styles.metaRow}><Icon name="map-marker-outline" color={colors.red} /><AppText style={styles.metaText}>{item.location}</AppText></View></View>
      <View style={styles.leadCard}><AppText style={styles.leadTitle}>About this event</AppText><AppText style={styles.leadText}>{item.overview ?? item.description}</AppText><InfoBanner text="Contact GEIC to confirm availability, attendance mode and venue before making plans." /></View>
      <Bullets title="What you can explore" items={item.highlights} />
      <PrimaryButton label="Register my interest" icon="calendar-check-outline" onPress={() => navigation.navigate('Booking', { kind: 'event', referenceId: item.id, title: item.title })} />
    </DetailScreenShell>
  );
}

function validateDraft(draft: EnquiryDraft) {
  const errors: Partial<Record<keyof EnquiryDraft, string>> = {};
  if (!draft.fullName.trim()) errors.fullName = 'Enter your name.';
  if (!/^\S+@\S+\.\S+$/.test(draft.email.trim())) errors.email = 'Enter a valid email address.';
  if (!/^[0-9+()\-\s]{7,24}$/.test(draft.phone.trim())) errors.phone = 'Enter a valid phone number.';
  if (!draft.consent) errors.consent = 'Please allow a counsellor to contact you.';
  return errors;
}

export function BookingScreen({ route, navigation }: NativeStackScreenProps<RootStackParamList, 'Booking'>) {
  const { enquiryDraft, updateEnquiryDraft, clearEnquiryDraft } = useAppState();
  const [errors, setErrors] = useState<Partial<Record<keyof EnquiryDraft, string>>>({});
  const [status, setStatus] = useState<{ tone: 'error' | 'success'; text: string } | null>(null);
  const [loading, setLoading] = useState(false);
  const context = route.params ?? { kind: 'counselling' as const };

  function update(field: keyof EnquiryDraft, value: string | boolean) {
    updateEnquiryDraft({ ...enquiryDraft, [field]: value });
    setErrors((current) => ({ ...current, [field]: undefined }));
  }

  async function submit() {
    const nextErrors = validateDraft(enquiryDraft);
    setErrors(nextErrors);
    if (Object.keys(nextErrors).length) {
      setStatus({ tone: 'error', text: 'Please check the highlighted fields.' });
      return;
    }
    setLoading(true);
    setStatus(null);
    try {
      const result = await submitEnquiry(enquiryDraft, { kind: context.kind ?? 'counselling', referenceId: context.referenceId });
      clearEnquiryDraft();
      setStatus({ tone: 'success', text: `${result.message} Reference: ${result.reference}` });
    } catch (caught) {
      const apiError = caught instanceof ApiError ? caught : null;
      setStatus({ tone: 'error', text: apiError?.message ?? 'Your enquiry could not be sent. Please try again.' });
      if (apiError?.errors) {
        setErrors({
          fullName: apiError.errors.fullName?.[0],
          email: apiError.errors.email?.[0],
          phone: apiError.errors.phone?.[0],
          consent: apiError.errors.consent?.[0],
        });
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <DetailScreenShell title={context.title ?? 'Book free counselling'} onBack={navigation.goBack}>
        <View style={styles.bookingIntro}><Icon name="calendar-check-outline" size={32} color={colors.red} /><AppText style={styles.bookingTitle}>Tell us where you want to go</AppText><AppText style={styles.bookingCopy}>Your draft stays on this device until you submit it. Required fields are marked below.</AppText></View>
        <View style={styles.formCard}>
          <TextField label="Your name *" value={enquiryDraft.fullName} onChangeText={(value) => update('fullName', value)} autoComplete="name" error={errors.fullName} />
          <TextField label="Email address *" value={enquiryDraft.email} onChangeText={(value) => update('email', value)} keyboardType="email-address" autoCapitalize="none" autoComplete="email" error={errors.email} />
          <TextField label="Phone number *" value={enquiryDraft.phone} onChangeText={(value) => update('phone', value)} keyboardType="phone-pad" autoComplete="tel" error={errors.phone} />
          <TextField label="City" value={enquiryDraft.city} onChangeText={(value) => update('city', value)} placeholder="Your current city" />
          <TextField label="Preferred destination" value={enquiryDraft.destination} onChangeText={(value) => update('destination', value)} placeholder="For example, Australia" />
          <TextField label="Study level" value={enquiryDraft.studyLevel} onChangeText={(value) => update('studyLevel', value)} placeholder="For example, Postgraduate" />
          <TextField label="Preferred intake" value={enquiryDraft.preferredIntake} onChangeText={(value) => update('preferredIntake', value)} placeholder="For example, September 2027" />
          <TextField label="Preferred course" value={enquiryDraft.preferredCourse} onChangeText={(value) => update('preferredCourse', value)} placeholder="What would you like to study?" />
          <TextField label="English test" value={enquiryDraft.englishTest} onChangeText={(value) => update('englishTest', value)} placeholder="IELTS, PTE, TOEFL or not sure" />
          <TextField label="Anything else?" value={enquiryDraft.message} onChangeText={(value) => update('message', value)} placeholder="Share your goals or questions" multiline />
          <Pressable accessibilityRole="checkbox" accessibilityState={{ checked: enquiryDraft.consent }} onPress={() => update('consent', !enquiryDraft.consent)} style={({ pressed }) => [styles.consentRow, pressed && styles.pressed]}>
            <View style={[styles.checkbox, enquiryDraft.consent && styles.checkboxChecked]}><Icon name={enquiryDraft.consent ? 'check' : 'checkbox-blank-outline'} size={21} color={enquiryDraft.consent ? colors.white : colors.muted} /></View>
            <AppText style={styles.consentText}>I allow a GEIC counsellor to contact me about this enquiry.</AppText>
          </Pressable>
          {errors.consent ? <AppText style={styles.errorText}>{errors.consent}</AppText> : null}
          {status ? <InfoBanner text={status.text} tone={status.tone} /> : null}
          {status?.tone !== 'success' ? <PrimaryButton label={loading ? 'Sending enquiry…' : 'Submit counselling request'} icon="send-outline" disabled={loading} onPress={() => void submit()} /> : <PrimaryButton label="Return home" icon="home-outline" onPress={() => navigation.navigate('MainTabs')} />}
        </View>
      </DetailScreenShell>
    </KeyboardAvoidingView>
  );
}

export function NotificationsScreen({ navigation }: NativeStackScreenProps<RootStackParamList, 'Notifications'>) {
  const { catalog, readNotificationIds, markNotificationRead, markAllNotificationsRead } = useAppState();
  const notifications = catalog.events.map((event) => ({ id: `event:${event.id}`, title: event.title, body: `${event.date} · ${event.location}`, eventId: event.id }));
  return (
    <DetailScreenShell title="Notifications" onBack={navigation.goBack}>
      <View style={styles.notificationHeader}><View style={{ flex: 1 }}><AppText style={styles.sectionTitle}>Event updates</AppText><AppText style={styles.notificationCopy}>Read state is saved on this device.</AppText></View><Pressable accessibilityRole="button" onPress={markAllNotificationsRead} style={styles.markAll}><AppText style={styles.markAllText}>Mark all read</AppText></Pressable></View>
      <View style={styles.rows}>{notifications.map((item) => { const read = readNotificationIds.includes(item.id); return <Pressable key={item.id} accessibilityRole="button" accessibilityLabel={`${item.title}, ${read ? 'read' : 'unread'}`} onPress={() => { markNotificationRead(item.id); navigation.navigate('Event', { id: item.eventId }); }} style={({ pressed }) => [styles.notification, !read && styles.notificationUnread, pressed && styles.pressed]}><View style={[styles.notificationIcon, !read && styles.notificationIconUnread]}><Icon name="calendar-star" color={read ? colors.muted : colors.red} /></View><View style={{ flex: 1 }}><AppText style={styles.notificationTitle}>{item.title}</AppText><AppText style={styles.notificationCopy}>{item.body}</AppText></View>{!read ? <View accessibilityLabel="Unread" style={styles.unreadDot} /> : null}</Pressable>; })}</View>
    </DetailScreenShell>
  );
}

const styles = StyleSheet.create({
  pressed: { opacity: 0.74 }, hero: { minHeight: 300, borderRadius: 24, overflow: 'hidden', justifyContent: 'flex-end', backgroundColor: colors.navyDeep, ...shadows.card }, heroImage: { opacity: 0.94 }, heroShade: { position: 'absolute', inset: 0, backgroundColor: 'rgba(7, 20, 42, 0.56)' }, heroCopy: { padding: 20 }, eyebrow: { color: '#FFB8BB', fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.3 }, heroTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 31, lineHeight: 37, marginTop: 7 }, heroText: { color: '#E8EEF7', fontFamily: fonts.medium, fontSize: 13, lineHeight: 20, marginTop: 7 },
  leadCard: { borderRadius: 20, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, padding: 18, marginTop: 14, ...shadows.card }, leadTitle: { fontFamily: fonts.extraBold, fontSize: 20, lineHeight: 26 }, leadText: { color: colors.muted, fontSize: 13, lineHeight: 21, marginTop: 8 }, section: { marginVertical: 24 }, sectionTitle: { fontFamily: fonts.extraBold, fontSize: 20, marginBottom: 12 }, rows: { gap: 8 }, dataRow: { minHeight: 58, borderRadius: 14, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 12, flexDirection: 'row', alignItems: 'center', gap: 12 }, dataLabel: { flex: 1, color: colors.muted, fontSize: 12 }, dataValue: { flex: 1, textAlign: 'right', fontFamily: fonts.bold, fontSize: 12 }, bullet: { flexDirection: 'row', alignItems: 'flex-start', gap: 9, marginVertical: 6 }, bulletText: { flex: 1, color: colors.muted, fontSize: 13, lineHeight: 20 }, tagWrap: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 }, tag: { color: colors.navy, backgroundColor: colors.softBlue, borderRadius: 10, paddingHorizontal: 10, paddingVertical: 8, fontFamily: fonts.bold, fontSize: 10, overflow: 'hidden' },
  processRow: { minHeight: 76, flexDirection: 'row', alignItems: 'flex-start', paddingVertical: 8 }, processNumber: { width: 40, height: 40, borderRadius: 13, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 12 }, processNumberText: { color: colors.red, fontFamily: fonts.extraBold }, processTitle: { fontFamily: fonts.bold, fontSize: 14 }, processCopy: { color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 4 },
  metaCard: { borderRadius: 18, backgroundColor: colors.softBlue, padding: 15, gap: 10, marginTop: 14 }, metaRow: { flexDirection: 'row', alignItems: 'center', gap: 10 }, metaText: { flex: 1, fontFamily: fonts.semiBold, fontSize: 13 },
  bookingIntro: { alignItems: 'center', paddingVertical: 10, paddingHorizontal: 18 }, bookingTitle: { fontFamily: fonts.extraBold, fontSize: 24, textAlign: 'center', marginTop: 10 }, bookingCopy: { color: colors.muted, fontSize: 13, lineHeight: 20, textAlign: 'center', marginTop: 7 }, formCard: { borderRadius: 20, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 16, ...shadows.card }, consentRow: { minHeight: 64, flexDirection: 'row', alignItems: 'center', marginBottom: 4 }, checkbox: { width: 42, height: 42, borderRadius: 13, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.canvas, alignItems: 'center', justifyContent: 'center', marginRight: 11 }, checkboxChecked: { backgroundColor: colors.success, borderColor: colors.success }, consentText: { flex: 1, color: colors.muted, fontSize: 12, lineHeight: 18 }, errorText: { color: colors.danger, fontFamily: fonts.medium, fontSize: 12, marginBottom: 8 },
  notificationHeader: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 18 }, notificationCopy: { color: colors.muted, fontSize: 11, lineHeight: 16, marginTop: 3 }, markAll: { minHeight: 48, justifyContent: 'center', paddingHorizontal: 6 }, markAllText: { color: colors.red, fontFamily: fonts.bold, fontSize: 12 }, notification: { minHeight: 82, borderRadius: 17, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, padding: 12, flexDirection: 'row', alignItems: 'center', gap: 11 }, notificationUnread: { borderColor: '#F2A7AA', backgroundColor: '#FFF9F9' }, notificationIcon: { width: 44, height: 44, borderRadius: 14, backgroundColor: colors.canvas, alignItems: 'center', justifyContent: 'center' }, notificationIconUnread: { backgroundColor: colors.redSoft }, notificationTitle: { fontFamily: fonts.bold, fontSize: 13 }, unreadDot: { width: 9, height: 9, borderRadius: 5, backgroundColor: colors.red },
});

import { StatusBar } from 'expo-status-bar';
import {
  Image,
  ImageBackground,
  Pressable,
  SafeAreaView,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  useWindowDimensions,
  View,
} from 'react-native';
import { useMemo, useState } from 'react';

type Tab = 'Home' | 'Explore' | 'Services' | 'Apply';

const colors = {
  navy: '#0E234A', navySoft: '#1B3767', red: '#ED1C24', redSoft: '#FFF0F1',
  ink: '#12213F', muted: '#65738B', line: '#E4E9F1', canvas: '#F5F8FC', white: '#FFFFFF',
};

const destinations = [
  { name: 'Australia', detail: 'Top universities · 12k+ courses', image: require('./assets/australia.jpg') },
  { name: 'Canada', detail: 'Work-friendly study routes', image: require('./assets/canada.jpg') },
  { name: 'United Kingdom', detail: 'One-year master’s options', image: require('./assets/uk.jpg') },
];

const tools = [
  { id: 'compare', title: 'Compare destinations', caption: 'Find your best-fit country', mark: 'CD', tone: 'blue' },
  { id: 'emi', title: 'EMI calculator', caption: 'Plan your study budget', mark: '₹', tone: 'orange' },
  { id: 'loans', title: 'Education loans', caption: 'Explore funding options', mark: 'EL', tone: 'green' },
  { id: 'agents', title: 'AI study agents', caption: 'Get instant guidance', mark: 'AI', tone: 'purple' },
];

export default function App() {
  const { width } = useWindowDimensions();
  const [activeTab, setActiveTab] = useState<Tab>('Home');
  const [search, setSearch] = useState('');
  const [notice, setNotice] = useState('');
  const filteredTools = useMemo(() => {
    const query = search.trim().toLowerCase();
    return query ? tools.filter((tool) => `${tool.title} ${tool.caption}`.toLowerCase().includes(query)) : tools;
  }, [search]);
  const showNotice = (message: string) => { setNotice(message); setTimeout(() => setNotice(''), 2600); };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar style="light" />
      <View style={styles.appShell}>
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">
          <View style={styles.topBar}>
            <View style={styles.brandRow}>
              <View style={styles.logoFrame}><Image source={require('./assets/trans-globe-logo.png')} style={styles.logo} accessibilityLabel="Trans Globe logo" /></View>
              <View><Text style={styles.brandName}>Trans Globe</Text><Text style={styles.brandMeta}>GLOBAL EDUCATION SPECIALISTS</Text></View>
            </View>
            <Pressable accessibilityRole="button" accessibilityLabel="Open profile" onPress={() => setActiveTab('Apply')} style={({ pressed }) => [styles.avatar, pressed && styles.pressed]}><Text style={styles.avatarText}>S</Text></Pressable>
          </View>

          <View style={styles.greetingRow}>
            <View style={{ flex: 1 }}><Text style={styles.eyebrow}>YOUR GLOBAL JOURNEY</Text><Text style={styles.greeting}>Good morning, future global student.</Text><Text style={styles.subGreeting}>Small steps today. Big opportunities tomorrow.</Text></View>
            <View style={styles.streakPill}><Text style={styles.streakNumber}>04</Text><Text style={styles.streakLabel}>day streak</Text></View>
          </View>

          <ImageBackground source={require('./assets/student-guidance.jpg')} style={styles.hero} imageStyle={styles.heroImage}>
            <View style={styles.heroShade} /><View style={styles.heroContent}>
              <View style={styles.heroTag}><Text style={styles.heroTagText}>START WITH CLARITY</Text></View>
              <Text style={styles.heroTitle}>Your next chapter{`\n`}starts here.</Text>
              <Text style={styles.heroCopy}>Shortlist a destination, compare costs and get guidance from a Trans Globe counsellor.</Text>
              <Pressable accessibilityRole="button" accessibilityLabel="Book a free counselling session" onPress={() => showNotice('We will connect you with a counsellor')} style={({ pressed }) => [styles.primaryButton, pressed && styles.pressed]}><Text style={styles.primaryButtonText}>Book free counselling</Text><Text style={styles.buttonArrow}>→</Text></Pressable>
            </View>
            <View style={styles.heroStat}><Text style={styles.heroStatValue}>70,250+</Text><Text style={styles.heroStatLabel}>students placed worldwide</Text></View>
          </ImageBackground>

          <View style={styles.sectionHeader}><View><Text style={styles.sectionTitle}>Continue your plan</Text><Text style={styles.sectionCaption}>Pick up where you left off</Text></View><Pressable accessibilityRole="button" accessibilityLabel="View all learning plans" onPress={() => setActiveTab('Explore')} style={({ pressed }) => [styles.linkButton, pressed && styles.pressed]}><Text style={styles.linkText}>View all</Text></Pressable></View>
          <Pressable accessibilityRole="button" accessibilityLabel="Open destination discovery course" onPress={() => showNotice('Destination discovery is ready to explore')} style={({ pressed }) => [styles.progressCard, pressed && styles.pressed]}>
            <View style={styles.progressIcon}><Text style={styles.progressIconText}>01</Text></View><View style={styles.progressBody}><Text style={styles.progressKicker}>MODULE 1 OF 5</Text><Text style={styles.progressTitle}>Destination discovery</Text><Text style={styles.progressCopy}>Understand tuition, visas and career outcomes.</Text><View style={styles.progressTrack}><View style={[styles.progressFill, { width: '42%' }]} /></View><Text style={styles.progressMeta}>42% complete</Text></View><Text style={styles.chevron}>›</Text>
          </Pressable>

          <View style={styles.sectionHeader}><View><Text style={styles.sectionTitle}>Smart tools for you</Text><Text style={styles.sectionCaption}>Make decisions with confidence</Text></View></View>
          <TextInput value={search} onChangeText={setSearch} placeholder="Search tools and guidance" placeholderTextColor="#8A96A8" accessibilityLabel="Search tools and guidance" style={styles.searchInput} />
          <View style={styles.toolGrid}>{filteredTools.map((tool) => <Pressable key={tool.id} accessibilityRole="button" accessibilityLabel={tool.title} onPress={() => showNotice(`${tool.title} is ready to explore`)} style={({ pressed }) => [styles.toolCard, pressed && styles.pressed]}><View style={[styles.toolMark, tool.tone === 'orange' && styles.toolMarkOrange, tool.tone === 'green' && styles.toolMarkGreen, tool.tone === 'purple' && styles.toolMarkPurple]}><Text style={styles.toolMarkText}>{tool.mark}</Text></View><Text style={styles.toolTitle}>{tool.title}</Text><Text style={styles.toolCaption}>{tool.caption}</Text></Pressable>)}</View>

          <View style={styles.sectionHeader}><View><Text style={styles.sectionTitle}>Popular destinations</Text><Text style={styles.sectionCaption}>Explore where your future could take you</Text></View><Pressable accessibilityRole="button" accessibilityLabel="Explore all destinations" onPress={() => setActiveTab('Explore')} style={({ pressed }) => [styles.linkButton, pressed && styles.pressed]}><Text style={styles.linkText}>Explore</Text></Pressable></View>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.destinationRow}>{destinations.map((destination) => <Pressable key={destination.name} accessibilityRole="button" accessibilityLabel={`Explore ${destination.name}`} onPress={() => showNotice(`${destination.name} is ready to explore`)} style={({ pressed }) => [styles.destinationCard, pressed && styles.pressed]}><Image source={destination.image} style={styles.destinationImage} /><View style={styles.destinationOverlay} /><View style={styles.destinationText}><Text style={styles.destinationName}>{destination.name}</Text><Text style={styles.destinationDetail}>{destination.detail}</Text></View></Pressable>)}</ScrollView>

          <View style={styles.eventCard}><View style={styles.eventDate}><Text style={styles.eventDateMonth}>AUG</Text><Text style={styles.eventDateDay}>24</Text></View><View style={styles.eventBody}><Text style={styles.eventKicker}>UPCOMING EVENT · INDORE</Text><Text style={styles.eventTitle}>Global Uni Expo 2026</Text><Text style={styles.eventCopy}>Meet university representatives from Europe and Dubai.</Text></View><Pressable accessibilityRole="button" accessibilityLabel="View Global Uni Expo details" onPress={() => showNotice('Event details coming soon')} style={({ pressed }) => [styles.eventButton, pressed && styles.pressed]}><Text style={styles.eventButtonText}>View</Text></Pressable></View>
          <View style={styles.bottomSpacer} />
        </ScrollView>
        {notice ? <View accessibilityLiveRegion="polite" style={styles.notice}><Text style={styles.noticeText}>{notice}</Text></View> : null}
        <View style={[styles.bottomNav, width >= 700 && styles.bottomNavWide]}>{(['Home', 'Explore', 'Services', 'Apply'] as Tab[]).map((tab) => { const selected = activeTab === tab; return <Pressable key={tab} accessibilityRole="tab" accessibilityState={{ selected }} accessibilityLabel={`${tab} tab`} onPress={() => setActiveTab(tab)} style={({ pressed }) => [styles.navItem, selected && styles.navItemSelected, pressed && styles.pressed]}><View style={[styles.navMark, selected && styles.navMarkSelected]}><Text style={[styles.navMarkText, selected && styles.navMarkTextSelected]}>{tab === 'Home' ? '⌂' : tab === 'Explore' ? '◎' : tab === 'Services' ? '◇' : '＋'}</Text></View><Text style={[styles.navLabel, selected && styles.navLabelSelected]}>{tab}</Text></Pressable>; })}</View>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: colors.navy }, appShell: { flex: 1, backgroundColor: colors.canvas },
  scrollContent: { paddingHorizontal: 20, paddingTop: 14, paddingBottom: 24, maxWidth: 900, width: '100%', alignSelf: 'center' },
  topBar: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 22 }, brandRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  logoFrame: { width: 42, height: 42, borderRadius: 14, backgroundColor: colors.white, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: colors.line }, logo: { width: 34, height: 34, resizeMode: 'contain' },
  brandName: { color: colors.navy, fontSize: 17, fontWeight: '800', letterSpacing: -0.2 }, brandMeta: { color: colors.muted, fontSize: 7, fontWeight: '700', letterSpacing: 0.7, marginTop: 2 },
  avatar: { width: 44, height: 44, borderRadius: 22, backgroundColor: colors.navy, alignItems: 'center', justifyContent: 'center' }, avatarText: { color: colors.white, fontSize: 18, fontWeight: '800' },
  greetingRow: { flexDirection: 'row', alignItems: 'flex-end', marginBottom: 18 }, eyebrow: { color: colors.red, fontSize: 11, fontWeight: '800', letterSpacing: 1.2, marginBottom: 7 }, greeting: { color: colors.ink, fontSize: 25, lineHeight: 30, fontWeight: '800', letterSpacing: -0.5 }, subGreeting: { color: colors.muted, fontSize: 13, marginTop: 6 },
  streakPill: { backgroundColor: colors.white, borderRadius: 15, paddingVertical: 9, paddingHorizontal: 12, alignItems: 'center', borderWidth: 1, borderColor: colors.line, marginLeft: 8 }, streakNumber: { color: colors.red, fontSize: 16, fontWeight: '800' }, streakLabel: { color: colors.muted, fontSize: 9, marginTop: 1 },
  hero: { minHeight: 232, borderRadius: 24, overflow: 'hidden', backgroundColor: colors.navy, marginBottom: 28 }, heroImage: { opacity: 0.22, resizeMode: 'cover' }, heroShade: { ...StyleSheet.absoluteFill, backgroundColor: 'rgba(14,35,74,0.56)' }, heroContent: { padding: 23, maxWidth: 540 }, heroTag: { alignSelf: 'flex-start', borderRadius: 99, backgroundColor: 'rgba(255,255,255,0.17)', paddingHorizontal: 10, paddingVertical: 6, marginBottom: 12 }, heroTagText: { color: '#FFD0D2', fontSize: 9, letterSpacing: 1.1, fontWeight: '800' }, heroTitle: { color: colors.white, fontSize: 29, lineHeight: 33, fontWeight: '800', letterSpacing: -0.7 }, heroCopy: { color: '#D4DEEF', fontSize: 13, lineHeight: 19, marginTop: 10, maxWidth: 390 },
  primaryButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', alignSelf: 'flex-start', minHeight: 46, paddingHorizontal: 16, borderRadius: 13, backgroundColor: colors.red, marginTop: 17, gap: 12 }, primaryButtonText: { color: colors.white, fontSize: 13, fontWeight: '800' }, buttonArrow: { color: colors.white, fontSize: 18, lineHeight: 18 }, heroStat: { position: 'absolute', right: 18, bottom: 17, backgroundColor: colors.white, borderRadius: 13, paddingVertical: 9, paddingHorizontal: 12 }, heroStatValue: { color: colors.navy, fontSize: 14, fontWeight: '800' }, heroStatLabel: { color: colors.muted, fontSize: 9, marginTop: 2 },
  sectionHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 12 }, sectionTitle: { color: colors.ink, fontSize: 19, fontWeight: '800', letterSpacing: -0.3 }, sectionCaption: { color: colors.muted, fontSize: 12, marginTop: 4 }, linkButton: { minHeight: 44, justifyContent: 'center', paddingHorizontal: 6 }, linkText: { color: colors.red, fontSize: 12, fontWeight: '800' },
  progressCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: colors.white, borderRadius: 18, padding: 15, borderWidth: 1, borderColor: colors.line, marginBottom: 27 }, progressIcon: { width: 48, height: 48, borderRadius: 15, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 13 }, progressIconText: { color: colors.red, fontSize: 16, fontWeight: '800' }, progressBody: { flex: 1 }, progressKicker: { color: colors.red, fontSize: 9, fontWeight: '800', letterSpacing: 0.9 }, progressTitle: { color: colors.ink, fontSize: 15, fontWeight: '800', marginTop: 4 }, progressCopy: { color: colors.muted, fontSize: 11, marginTop: 3 }, progressTrack: { height: 6, borderRadius: 3, backgroundColor: '#E8EDF4', marginTop: 10, overflow: 'hidden' }, progressFill: { height: '100%', borderRadius: 3, backgroundColor: colors.red }, progressMeta: { color: colors.muted, fontSize: 10, marginTop: 5 }, chevron: { color: colors.navy, fontSize: 28, marginLeft: 8 },
  searchInput: { height: 48, borderRadius: 13, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, paddingHorizontal: 15, color: colors.ink, fontSize: 13, marginBottom: 12 }, toolGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10, marginBottom: 27 }, toolCard: { width: '48.8%', minHeight: 126, backgroundColor: colors.white, borderRadius: 17, borderWidth: 1, borderColor: colors.line, padding: 14 }, toolMark: { width: 36, height: 36, borderRadius: 12, backgroundColor: '#EAF2FF', alignItems: 'center', justifyContent: 'center', marginBottom: 12 }, toolMarkOrange: { backgroundColor: '#FFF3DE' }, toolMarkGreen: { backgroundColor: '#E6F8F0' }, toolMarkPurple: { backgroundColor: '#F0EBFF' }, toolMarkText: { color: colors.navy, fontSize: 11, fontWeight: '800' }, toolTitle: { color: colors.ink, fontSize: 13, fontWeight: '800', lineHeight: 17 }, toolCaption: { color: colors.muted, fontSize: 10, marginTop: 5 },
  destinationRow: { gap: 12, paddingBottom: 4, marginBottom: 25 }, destinationCard: { width: 214, height: 145, borderRadius: 18, overflow: 'hidden', backgroundColor: colors.navy }, destinationImage: { ...StyleSheet.absoluteFill, resizeMode: 'cover' }, destinationOverlay: { ...StyleSheet.absoluteFill, backgroundColor: 'rgba(14,35,74,0.30)' }, destinationText: { position: 'absolute', left: 14, right: 10, bottom: 13 }, destinationName: { color: colors.white, fontSize: 17, fontWeight: '800' }, destinationDetail: { color: '#E4ECF8', fontSize: 10, marginTop: 3 },
  eventCard: { flexDirection: 'row', alignItems: 'center', backgroundColor: colors.navy, borderRadius: 18, padding: 15, marginBottom: 10 }, eventDate: { width: 48, height: 56, borderRadius: 13, backgroundColor: colors.red, alignItems: 'center', justifyContent: 'center', marginRight: 13 }, eventDateMonth: { color: '#FFD8DA', fontSize: 10, fontWeight: '800', letterSpacing: 1 }, eventDateDay: { color: colors.white, fontSize: 22, lineHeight: 24, fontWeight: '800' }, eventBody: { flex: 1 }, eventKicker: { color: '#FFBFC2', fontSize: 9, fontWeight: '800', letterSpacing: 0.8 }, eventTitle: { color: colors.white, fontSize: 15, fontWeight: '800', marginTop: 4 }, eventCopy: { color: '#C8D5EA', fontSize: 11, marginTop: 3 }, eventButton: { minWidth: 50, minHeight: 44, borderRadius: 12, borderWidth: 1, borderColor: '#58739E', alignItems: 'center', justifyContent: 'center', marginLeft: 10 }, eventButtonText: { color: colors.white, fontSize: 12, fontWeight: '800' }, bottomSpacer: { height: 88 },
  bottomNav: { position: 'absolute', bottom: 0, left: 0, right: 0, flexDirection: 'row', backgroundColor: colors.white, borderTopWidth: 1, borderTopColor: colors.line, paddingHorizontal: 8, paddingTop: 8, paddingBottom: 8 }, bottomNavWide: { maxWidth: 900, alignSelf: 'center', width: '100%', borderLeftWidth: 1, borderRightWidth: 1, borderColor: colors.line }, navItem: { flex: 1, minHeight: 58, borderRadius: 14, alignItems: 'center', justifyContent: 'center' }, navItemSelected: { backgroundColor: colors.redSoft }, navMark: { width: 27, height: 27, alignItems: 'center', justifyContent: 'center' }, navMarkSelected: { backgroundColor: colors.red, borderRadius: 9 }, navMarkText: { color: '#71809A', fontSize: 21, lineHeight: 23, fontWeight: '700' }, navMarkTextSelected: { color: colors.white, fontSize: 17 }, navLabel: { color: '#71809A', fontSize: 10, fontWeight: '700', marginTop: 3 }, navLabelSelected: { color: colors.red }, notice: { position: 'absolute', left: 20, right: 20, bottom: 84, borderRadius: 13, paddingVertical: 12, paddingHorizontal: 16, backgroundColor: colors.ink, alignItems: 'center' }, noticeText: { color: colors.white, fontSize: 12, fontWeight: '700' }, pressed: { opacity: 0.76 },
});

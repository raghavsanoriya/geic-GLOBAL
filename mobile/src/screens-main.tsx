import type { NavigationProp } from '@react-navigation/native';
import { useNavigation } from '@react-navigation/native';
import React, { useRef, useState } from 'react';
import {
  ActivityIndicator,
  Image,
  ImageBackground,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  useWindowDimensions,
  View,
} from 'react-native';
import { ApiError, evaluateProfile, sendChat } from './api';
import { useAppState } from './app-state';
import { documentChecklist, starterPrompts } from './catalog';
import { AppHeader, AppScreen, AppText, ChoiceChip, HeroCarousel, Icon, imageSource, InfoBanner, PrimaryButton, SectionTitle, TextField } from './components';
import { colors, fonts, shadows } from './theme';
import type { ChatMessage, EvaluationResult, RootStackParamList, RootTabParamList } from './types';

type AppNavigation = NavigationProp<RootStackParamList & RootTabParamList>;

function useAppNavigation() {
  return useNavigation<AppNavigation>();
}

function Header() {
  const navigation = useAppNavigation();
  return <AppHeader onNavigate={(tab) => navigation.navigate(tab)} onNotifications={() => navigation.navigate('Notifications')} onBooking={() => navigation.navigate('Booking', { kind: 'counselling', title: 'Free counselling' })} />;
}

function DestinationCard({ id }: { id: string }) {
  const navigation = useAppNavigation();
  const { catalog } = useAppState();
  const destination = catalog.destinations.find((item) => item.id === id);
  if (!destination) return null;
  return (
    <Pressable accessibilityRole="button" accessibilityLabel={`Open ${destination.name} destination`} onPress={() => navigation.navigate('Destination', { id })} style={({ pressed }) => [styles.destinationCard, pressed && styles.pressed]}>
      <ImageBackground source={imageSource(destination)} style={styles.fill} imageStyle={styles.cardImage}>
        <View style={styles.imageShade} />
        <View style={styles.destinationBadge}><AppText style={styles.destinationBadgeText}>{destination.stat}</AppText></View>
        <View style={styles.destinationCopy}>
          <AppText style={styles.destinationName}>{destination.name}</AppText>
          <AppText style={styles.destinationTagline}>{destination.tagline}</AppText>
        </View>
      </ImageBackground>
    </Pressable>
  );
}

export function HomeScreen() {
  const navigation = useAppNavigation();
  const { width } = useWindowDimensions();
  const { catalog, catalogSource, isRefreshing, refreshCatalog } = useAppState();
  const tileWidth = width >= 700 ? '31.8%' : '48.2%';
  const quickActions = [
    { title: 'Explore countries', copy: 'Compare your options', icon: 'earth', action: () => navigation.navigate('Explore') },
    { title: 'AI Advisor', copy: 'Ask your next question', icon: 'robot-outline', action: () => navigation.navigate('Advisor') },
    { title: 'Evaluate profile', copy: 'Check your readiness', icon: 'clipboard-check-outline', action: () => navigation.navigate('Evaluate') },
    { title: 'Book counselling', copy: 'Create a clear plan', icon: 'calendar-check-outline', action: () => navigation.navigate('Booking', { kind: 'counselling', title: 'Free counselling' }) },
  ];
  const featuredEvent = catalog.events[0];

  return (
    <AppScreen header={<Header />} refreshing={isRefreshing} onRefresh={() => void refreshCatalog()}>
      <HeroCarousel slides={catalog.destinations.slice(0, 3)} onOpen={(id) => navigation.navigate('Destination', { id })} />
      <InfoBanner text={catalogSource === 'verified-live' ? 'Showing the latest verified GEIC catalogue.' : 'Showing the verified offline guide. Pull to refresh when you are online.'} />

      <SectionTitle eyebrow="START HERE" title="Make your next decision easier" copy="Explore, evaluate and speak with the Indore team when you need personal guidance." />
      <View style={styles.quickGrid}>
        {quickActions.map((item) => (
          <Pressable key={item.title} accessibilityRole="button" accessibilityLabel={item.title} onPress={item.action} style={({ pressed }) => [styles.quickTile, { width: tileWidth }, pressed && styles.pressed]}>
            <View style={styles.quickIcon}><Icon name={item.icon} color={colors.red} /></View>
            <AppText style={styles.quickTitle}>{item.title}</AppText>
            <AppText style={styles.quickCopy}>{item.copy}</AppText>
          </Pressable>
        ))}
      </View>

      <SectionTitle eyebrow="EXPLORE" title="Study destinations" copy="Start with verified country guidance, then confirm course-specific requirements." action="See all" onAction={() => navigation.navigate('Explore')} />
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.horizontalRail}>
        {catalog.destinations.slice(0, 6).map((destination) => <DestinationCard key={destination.id} id={destination.id} />)}
      </ScrollView>

      <SectionTitle eyebrow="YOUR ROADMAP" title="Four useful milestones" />
      <View style={styles.roadmap}>
        {[
          ['01', 'Discover', 'Clarify your course, country, budget and career goals.'],
          ['02', 'Shortlist', 'Compare institutions and confirm official requirements.'],
          ['03', 'Apply', 'Prepare documents and submit complete applications.'],
          ['04', 'Prepare', 'Review your offer, finances, visa evidence and departure plan.'],
        ].map(([number, title, copy]) => (
          <View key={number} style={styles.roadmapRow}>
            <View style={styles.roadmapNumber}><AppText style={styles.roadmapNumberText}>{number}</AppText></View>
            <View style={styles.roadmapCopy}><AppText style={styles.roadmapTitle}>{title}</AppText><AppText style={styles.roadmapText}>{copy}</AppText></View>
          </View>
        ))}
      </View>

      {featuredEvent ? <>
        <SectionTitle eyebrow="EVENTS" title="Meet, ask, compare" copy="Confirm current availability with the GEIC team before making travel plans." action="More" onAction={() => navigation.navigate('More')} />
        <Pressable accessibilityRole="button" accessibilityLabel={`Open ${featuredEvent.title}`} onPress={() => navigation.navigate('Event', { id: featuredEvent.id })} style={({ pressed }) => [styles.eventCard, pressed && styles.pressed]}>
          <Image source={imageSource(featuredEvent)} style={styles.eventImage} />
          <View style={styles.eventCopy}><AppText style={styles.cardKicker}>{featuredEvent.type.toUpperCase()}</AppText><AppText style={styles.cardTitle}>{featuredEvent.title}</AppText><AppText style={styles.cardText}>{featuredEvent.date} · {featuredEvent.time}</AppText><AppText style={styles.cardText}>{featuredEvent.location}</AppText></View>
        </Pressable>
      </> : null}

      <View style={styles.finalCta}>
        <AppText style={styles.finalKicker}>FREE COUNSELLING</AppText>
        <AppText style={styles.finalTitle}>Turn research into a practical study plan.</AppText>
        <AppText style={styles.finalCopy}>Share your profile and the Indore team will help you identify the next useful step.</AppText>
        <PrimaryButton label="Book my conversation" icon="calendar-check-outline" onPress={() => navigation.navigate('Booking', { kind: 'counselling', title: 'Free counselling' })} />
      </View>
    </AppScreen>
  );
}

export function ExploreScreen() {
  const navigation = useAppNavigation();
  const { catalog, isRefreshing, refreshCatalog } = useAppState();
  const [query, setQuery] = useState('');
  const filtered = catalog.destinations.filter((destination) => `${destination.name} ${destination.tagline} ${destination.highlights.join(' ')}`.toLowerCase().includes(query.trim().toLowerCase()));

  return (
    <AppScreen header={<Header />} refreshing={isRefreshing} onRefresh={() => void refreshCatalog()}>
      <SectionTitle eyebrow="EXPLORE" title="Find your place" copy="Search verified destination guidance and compare the essentials before you shortlist." />
      <View style={styles.searchBox}><Icon name="magnify" color={colors.muted} /><TextInput accessibilityLabel="Search destinations" value={query} onChangeText={setQuery} placeholder="Search destinations" placeholderTextColor="#727B8B" style={styles.searchInput} />{query ? <Pressable accessibilityRole="button" accessibilityLabel="Clear destination search" onPress={() => setQuery('')} style={styles.clearButton}><Icon name="close" size={18} /></Pressable> : null}</View>
      {filtered.length ? <View style={styles.listGap}>{filtered.map((destination) => (
        <Pressable key={destination.id} accessibilityRole="button" accessibilityLabel={`Open ${destination.name} destination`} onPress={() => navigation.navigate('Destination', { id: destination.id })} style={({ pressed }) => [styles.listCard, pressed && styles.pressed]}>
          <Image source={imageSource(destination)} style={styles.listImage} />
          <View style={styles.listCopy}><AppText style={styles.listTitle}>{destination.name}</AppText><AppText numberOfLines={2} style={styles.listText}>{destination.tagline}</AppText><View style={styles.pillRow}>{destination.highlights.slice(0, 2).map((item) => <AppText key={item} style={styles.pill}>{item}</AppText>)}</View></View>
          <Icon name="chevron-right" color={colors.red} />
        </Pressable>
      ))}</View> : <View style={styles.empty}><Icon name="map-search-outline" size={36} color={colors.red} /><AppText style={styles.emptyTitle}>No destinations found</AppText><AppText style={styles.emptyCopy}>Try another country or clear the search.</AppText><PrimaryButton label="Clear search" variant="secondary" icon="close" onPress={() => setQuery('')} /></View>}
    </AppScreen>
  );
}

export function AdvisorScreen() {
  const navigation = useAppNavigation();
  const [messages, setMessages] = useState<ChatMessage[]>([{ id: 'welcome', role: 'assistant', content: 'Hi! I’m the GEIC Study Advisor. Tell me your preferred course, study level, destination, budget or intake, and I’ll help you find a useful starting point.' }]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [failedMessage, setFailedMessage] = useState('');
  const scrollRef = useRef<ScrollView>(null);

  async function askAdvisor(value: string, appendUser = true) {
    const message = value.trim();
    if (!message || loading) return;
    const history = messages.slice(-10).map(({ role, content }) => ({ role, content }));
    if (appendUser) setMessages((current) => [...current, { id: `user-${Date.now()}`, role: 'user', content: message }]);
    setInput('');
    setError('');
    setLoading(true);
    try {
      const response = await sendChat(message, history);
      setMessages((current) => [...current, { id: `assistant-${Date.now()}`, role: 'assistant', content: response.reply }]);
      setFailedMessage('');
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : 'The advisor is unavailable. Please try again.');
      setFailedMessage(message);
    } finally {
      setLoading(false);
      setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), 50);
    }
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined} keyboardVerticalOffset={8}>
      <AppScreen header={<Header />} keyboardShouldPersistTaps="handled">
        <SectionTitle eyebrow="AI ADVISOR" title="Ask your next question" copy="Grounded in GEIC’s current catalogue. Course, university and visa requirements must still be officially confirmed." />
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.promptRail}>{starterPrompts.map((prompt) => <ChoiceChip key={prompt} label={prompt} selected={false} onPress={() => void askAdvisor(prompt)} />)}</ScrollView>
        <View style={styles.chatPanel}>
          <ScrollView ref={scrollRef} style={styles.chatList} contentContainerStyle={styles.chatListContent} nestedScrollEnabled>
            {messages.map((message) => <View key={message.id} style={[styles.bubble, message.role === 'user' ? styles.userBubble : styles.assistantBubble]}><AppText style={[styles.bubbleText, message.role === 'user' && styles.userBubbleText]}>{message.content}</AppText></View>)}
            {loading ? <View style={[styles.bubble, styles.assistantBubble, styles.loadingBubble]}><ActivityIndicator color={colors.red} /><AppText style={styles.loadingText}>Thinking through your question…</AppText></View> : null}
          </ScrollView>
          {error ? <View><InfoBanner text={error} tone="error" />{failedMessage ? <PrimaryButton label="Retry question" variant="secondary" icon="refresh" onPress={() => void askAdvisor(failedMessage, false)} /> : null}</View> : null}
          <View style={styles.chatComposer}>
            <TextInput accessibilityLabel="Message the AI Advisor" value={input} onChangeText={setInput} placeholder="Ask about courses, tests or applications" placeholderTextColor="#727B8B" multiline style={styles.chatInput} />
            <Pressable accessibilityRole="button" accessibilityLabel="Send message" accessibilityState={{ disabled: loading || !input.trim() }} disabled={loading || !input.trim()} onPress={() => void askAdvisor(input)} style={({ pressed }) => [styles.sendButton, (loading || !input.trim()) && styles.disabled, pressed && styles.pressed]}><Icon name="send" color={colors.white} /></Pressable>
          </View>
        </View>
        <PrimaryButton label="Book an Indore counselling call" icon="calendar-check-outline" onPress={() => navigation.navigate('Booking', { kind: 'counselling', title: 'Advisor follow-up' })} />
      </AppScreen>
    </KeyboardAvoidingView>
  );
}

const studyLevels = ['Undergraduate', 'Postgraduate', 'Diploma or pathway', 'Research'];
const englishTests = ['IELTS', 'PTE', 'TOEFL', 'Duolingo', 'Planning to take a test', 'Not sure yet'];

export function EvaluateScreen() {
  const navigation = useAppNavigation();
  const { catalog, checklist, toggleChecklist } = useAppState();
  const [academic, setAcademic] = useState('70');
  const [studyLevel, setStudyLevel] = useState('Postgraduate');
  const [destinations, setDestinations] = useState<string[]>([]);
  const [englishTest, setEnglishTest] = useState('Planning to take a test');
  const [englishScore, setEnglishScore] = useState('');
  const [intake, setIntake] = useState('');
  const [result, setResult] = useState<EvaluationResult | null>(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  function toggleDestination(name: string) {
    setDestinations((current) => current.includes(name) ? current.filter((item) => item !== name) : current.length < 3 ? [...current, name] : current);
  }

  async function runEvaluation() {
    const percentage = Number(academic);
    if (!Number.isFinite(percentage) || percentage < 35 || percentage > 100) return setError('Enter an academic percentage between 35 and 100.');
    if (!destinations.length) return setError('Choose at least one destination.');
    setLoading(true);
    setError('');
    try {
      setResult(await evaluateProfile({ academicPercentage: percentage, studyLevel, preferredDestinations: destinations, englishTest, englishScore: englishScore || undefined, preferredIntake: intake || undefined }));
    } catch (caught) {
      const apiError = caught instanceof ApiError ? caught : null;
      setError(apiError?.message ?? 'Evaluation is unavailable. Please try again.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <AppScreen header={<Header />}>
      <SectionTitle eyebrow="PROFILE EVALUATOR" title="See what to strengthen next" copy="Get planning guidance—not an admission, scholarship or visa guarantee." />
      <View style={styles.formCard}>
        <TextField label="Academic percentage" value={academic} onChangeText={setAcademic} keyboardType="decimal-pad" placeholder="70" />
        <AppText style={styles.choiceLabel}>Study level</AppText><View style={styles.chipWrap}>{studyLevels.map((item) => <ChoiceChip key={item} label={item} selected={studyLevel === item} onPress={() => setStudyLevel(item)} />)}</View>
        <AppText style={styles.choiceLabel}>Preferred destinations · choose up to 3</AppText><View style={styles.chipWrap}>{catalog.destinations.map((item) => <ChoiceChip key={item.id} label={item.name} selected={destinations.includes(item.name)} onPress={() => toggleDestination(item.name)} />)}</View>
        <AppText style={styles.choiceLabel}>English test</AppText><View style={styles.chipWrap}>{englishTests.map((item) => <ChoiceChip key={item} label={item} selected={englishTest === item} onPress={() => setEnglishTest(item)} />)}</View>
        <TextField label="Current or target test score (optional)" value={englishScore} onChangeText={setEnglishScore} placeholder="For example, IELTS 7.0" />
        <TextField label="Preferred intake (optional)" value={intake} onChangeText={setIntake} placeholder="For example, September 2027" />
        {error ? <InfoBanner text={error} tone="error" /> : null}
        <PrimaryButton label={loading ? 'Evaluating profile…' : 'Evaluate my profile'} icon="chart-donut" disabled={loading} onPress={() => void runEvaluation()} />
      </View>

      {result ? <View style={styles.resultCard}>
        <View style={styles.scoreCircle}><AppText style={styles.score}>{result.readinessScore}</AppText><AppText style={styles.scoreMeta}>/ 100</AppText></View>
        <AppText accessibilityRole="header" style={styles.resultTitle}>Your planning readiness</AppText><AppText style={styles.resultSummary}>{result.summary}</AppText>
        <AppText style={styles.resultLabel}>DESTINATIONS TO EXPLORE</AppText>{result.matches.map((match) => <Pressable key={match.id} accessibilityRole="button" onPress={() => navigation.navigate('Destination', { id: match.id })} style={({ pressed }) => [styles.matchRow, pressed && styles.pressed]}><View style={{ flex: 1 }}><AppText style={styles.matchName}>{match.name}</AppText><AppText style={styles.matchMeta}>{match.fit} · Next intake: {match.nextIntake}</AppText></View><Icon name="chevron-right" color={colors.red} /></Pressable>)}
        <AppText style={styles.resultLabel}>STRENGTHS</AppText>{result.strengths.map((item) => <Bullet key={item} text={item} icon="check-circle-outline" />)}
        <AppText style={styles.resultLabel}>NEXT ACTIONS</AppText>{result.actionItems.map((item) => <Bullet key={item} text={item} icon="arrow-right-circle-outline" />)}
        <InfoBanner text={result.disclaimer} />
        <PrimaryButton label="Discuss my result" icon="calendar-check-outline" onPress={() => navigation.navigate('Booking', { kind: 'counselling', title: 'Profile evaluation follow-up' })} />
      </View> : null}

      <SectionTitle eyebrow="DOCUMENTS" title={`${checklist.length} of ${documentChecklist.length} ready`} copy="Saved only on this device." />
      <View style={styles.listGap}>{documentChecklist.map((item) => { const checked = checklist.includes(item.id); return <Pressable key={item.id} accessibilityRole="checkbox" accessibilityState={{ checked }} onPress={() => toggleChecklist(item.id)} style={({ pressed }) => [styles.checkRow, pressed && styles.pressed]}><View style={[styles.checkBox, checked && styles.checkBoxChecked]}><Icon name={checked ? 'check' : 'file-outline'} size={20} color={checked ? colors.white : colors.muted} /></View><View style={{ flex: 1 }}><AppText style={styles.checkTitle}>{item.title}</AppText><AppText style={styles.checkHint}>{item.hint}</AppText></View></Pressable>; })}</View>
    </AppScreen>
  );
}

function Bullet({ text, icon }: { text: string; icon: string }) {
  return <View style={styles.bullet}><Icon name={icon} color={colors.red} size={19} /><AppText style={styles.bulletText}>{text}</AppText></View>;
}

export function MoreScreen() {
  const navigation = useAppNavigation();
  const { catalog } = useAppState();
  return (
    <AppScreen header={<Header />}>
      <SectionTitle eyebrow="MORE" title="Everything for your journey" copy="Services, events, tests, funding guidance and a direct route to the GEIC team." />
      <SectionTitle title="Services" />
      <View style={styles.listGap}>{catalog.services.map((service) => <Pressable key={service.id} accessibilityRole="button" accessibilityLabel={`Open ${service.title}`} onPress={() => navigation.navigate('Service', { id: service.id })} style={({ pressed }) => [styles.listCard, pressed && styles.pressed]}><View style={styles.serviceIcon}><Icon name={service.icon || 'briefcase-outline'} color={colors.red} /></View><View style={styles.listCopy}><AppText style={styles.listTitle}>{service.title}</AppText><AppText numberOfLines={2} style={styles.listText}>{service.description}</AppText></View><Icon name="chevron-right" color={colors.red} /></Pressable>)}</View>
      <SectionTitle title="Events" copy="Confirm active event details before attending." />
      <View style={styles.listGap}>{catalog.events.map((event) => <Pressable key={event.id} accessibilityRole="button" accessibilityLabel={`Open ${event.title}`} onPress={() => navigation.navigate('Event', { id: event.id })} style={({ pressed }) => [styles.listCard, pressed && styles.pressed]}><View style={styles.dateTile}><Icon name="calendar-outline" color={colors.red} /></View><View style={styles.listCopy}><AppText style={styles.listTitle}>{event.title}</AppText><AppText style={styles.listText}>{event.date} · {event.time}</AppText></View><Icon name="chevron-right" color={colors.red} /></Pressable>)}</View>
      <SectionTitle title="Tests" copy="Choose a test only after checking the policies of your shortlisted institutions." />
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.horizontalRail}>{catalog.tests.map((test) => <Pressable key={test.id} accessibilityRole="button" onPress={() => navigation.navigate('Service', { id: 'test-preparation' })} style={({ pressed }) => [styles.miniCard, pressed && styles.pressed]}><Icon name="notebook-edit-outline" color={colors.red} /><AppText style={styles.miniTitle}>{test.title}</AppText><AppText style={styles.miniCopy}>{test.description}</AppText></Pressable>)}</ScrollView>
      <SectionTitle title="Scholarship guidance" copy="Awards and deadlines vary by course, university, intake and profile." />
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.horizontalRail}>{catalog.scholarships.map((item) => <Pressable key={item.id} accessibilityRole="button" onPress={() => navigation.navigate('Service', { id: 'scholarship-guidance' })} style={({ pressed }) => [styles.miniCard, pressed && styles.pressed]}><Icon name="medal-outline" color={colors.red} /><AppText style={styles.miniTitle}>{item.name}</AppText><AppText style={styles.miniCopy}>{item.tagline}</AppText></Pressable>)}</ScrollView>
      <View style={styles.contactCard}><Icon name="map-marker-outline" color={colors.red} size={28} /><View style={{ flex: 1 }}><AppText style={styles.contactTitle}>{catalog.contact.office}</AppText><AppText style={styles.contactCopy}>{catalog.contact.email}</AppText></View></View>
      <View style={styles.finalCta}><AppText style={styles.finalKicker}>READY WHEN YOU ARE</AppText><AppText style={styles.finalTitle}>Build a clear, personal plan with GEIC.</AppText><AppText style={styles.finalCopy}>Tell us what you want to study and where you hope to go.</AppText><PrimaryButton label="Send my enquiry" icon="send-outline" onPress={() => navigation.navigate('Booking', { kind: 'counselling', title: 'Free counselling' })} /></View>
    </AppScreen>
  );
}

const styles = StyleSheet.create({
  fill: { flex: 1 }, pressed: { opacity: 0.74 }, disabled: { opacity: 0.45 }, imageShade: { position: 'absolute', inset: 0, backgroundColor: 'rgba(9, 25, 49, 0.45)' },
  quickGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10 }, quickTile: { minHeight: 132, borderRadius: 18, padding: 14, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, ...shadows.card }, quickIcon: { width: 42, height: 42, borderRadius: 14, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center' }, quickTitle: { fontFamily: fonts.bold, fontSize: 14, marginTop: 12 }, quickCopy: { color: colors.muted, fontSize: 11, lineHeight: 16, marginTop: 4 },
  horizontalRail: { gap: 12, paddingRight: 16, paddingBottom: 5 }, destinationCard: { width: 270, height: 196, borderRadius: 22, overflow: 'hidden', backgroundColor: colors.navy, ...shadows.card }, cardImage: { opacity: 0.94 }, destinationBadge: { position: 'absolute', top: 13, right: 13, backgroundColor: colors.white, paddingHorizontal: 9, paddingVertical: 6, borderRadius: 10 }, destinationBadgeText: { color: colors.navy, fontFamily: fonts.bold, fontSize: 10 }, destinationCopy: { position: 'absolute', left: 16, right: 16, bottom: 15 }, destinationName: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 24 }, destinationTagline: { color: '#EAF0F8', fontFamily: fonts.medium, fontSize: 11, lineHeight: 16, marginTop: 4 },
  roadmap: { gap: 8 }, roadmapRow: { flexDirection: 'row', borderRadius: 17, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, padding: 13 }, roadmapNumber: { width: 44, height: 44, borderRadius: 14, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 12 }, roadmapNumberText: { color: colors.red, fontFamily: fonts.extraBold }, roadmapCopy: { flex: 1 }, roadmapTitle: { fontFamily: fonts.bold, fontSize: 14 }, roadmapText: { color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 4 },
  eventCard: { minHeight: 168, borderRadius: 20, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, flexDirection: 'row', overflow: 'hidden', ...shadows.card }, eventImage: { width: '38%', minHeight: 168 }, eventCopy: { flex: 1, padding: 15, justifyContent: 'center' }, cardKicker: { color: colors.red, fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1 }, cardTitle: { fontFamily: fonts.extraBold, fontSize: 17, lineHeight: 22, marginTop: 6 }, cardText: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11, lineHeight: 16, marginTop: 5 },
  finalCta: { backgroundColor: colors.navyDeep, borderRadius: 24, padding: 20, marginTop: 30, gap: 10 }, finalKicker: { color: '#FFB8BB', fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.4 }, finalTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 24, lineHeight: 30 }, finalCopy: { color: '#DCE5F3', fontSize: 13, lineHeight: 20, marginBottom: 6 },
  searchBox: { minHeight: 54, borderRadius: 16, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, flexDirection: 'row', alignItems: 'center', paddingHorizontal: 14, marginBottom: 18 }, searchInput: { flex: 1, minHeight: 52, color: colors.ink, fontFamily: fonts.regular, fontSize: 15, marginLeft: 9 }, clearButton: { width: 44, height: 44, alignItems: 'center', justifyContent: 'center' },
  listGap: { gap: 10 }, listCard: { minHeight: 92, borderRadius: 17, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, padding: 10, flexDirection: 'row', alignItems: 'center', ...shadows.card }, listImage: { width: 78, height: 78, borderRadius: 13, marginRight: 12 }, listCopy: { flex: 1, minWidth: 0 }, listTitle: { fontFamily: fonts.bold, fontSize: 14 }, listText: { color: colors.muted, fontSize: 11, lineHeight: 16, marginTop: 4 }, pillRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 5, marginTop: 7 }, pill: { color: colors.navy, backgroundColor: colors.softBlue, borderRadius: 7, paddingHorizontal: 7, paddingVertical: 4, fontFamily: fonts.bold, fontSize: 9, overflow: 'hidden' },
  empty: { padding: 24, backgroundColor: colors.white, borderRadius: 20, alignItems: 'center', gap: 8 }, emptyTitle: { fontFamily: fonts.extraBold, fontSize: 18 }, emptyCopy: { color: colors.muted, fontSize: 13, marginBottom: 8 },
  promptRail: { gap: 8, paddingRight: 16, paddingBottom: 14 }, chatPanel: { minHeight: 315, borderRadius: 20, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 12, marginBottom: 14 }, chatList: { maxHeight: 210 }, chatListContent: { gap: 10, paddingBottom: 12 }, bubble: { maxWidth: '88%', borderRadius: 17, paddingHorizontal: 14, paddingVertical: 11 }, assistantBubble: { alignSelf: 'flex-start', backgroundColor: colors.softBlue, borderBottomLeftRadius: 5 }, userBubble: { alignSelf: 'flex-end', backgroundColor: colors.navy, borderBottomRightRadius: 5 }, bubbleText: { fontSize: 13, lineHeight: 20 }, userBubbleText: { color: colors.white }, loadingBubble: { flexDirection: 'row', alignItems: 'center', gap: 9 }, loadingText: { color: colors.muted, fontSize: 12 }, chatComposer: { minHeight: 58, flexDirection: 'row', alignItems: 'flex-end', gap: 8, borderTopWidth: 1, borderTopColor: colors.line, paddingTop: 10 }, chatInput: { flex: 1, maxHeight: 110, minHeight: 50, borderRadius: 14, backgroundColor: colors.canvas, color: colors.ink, fontFamily: fonts.regular, fontSize: 14, paddingHorizontal: 13, paddingVertical: 12 }, sendButton: { width: 50, height: 50, borderRadius: 16, backgroundColor: colors.red, alignItems: 'center', justifyContent: 'center' },
  formCard: { borderRadius: 20, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 16, ...shadows.card }, choiceLabel: { fontFamily: fonts.bold, fontSize: 13, marginTop: 6, marginBottom: 8 }, chipWrap: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 14 }, resultCard: { borderRadius: 22, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 18, marginTop: 18, ...shadows.card }, scoreCircle: { width: 90, height: 90, borderRadius: 45, backgroundColor: colors.navy, alignSelf: 'center', alignItems: 'center', justifyContent: 'center' }, score: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 32 }, scoreMeta: { color: '#C8D5E8', fontFamily: fonts.bold, fontSize: 11 }, resultTitle: { fontFamily: fonts.extraBold, fontSize: 21, textAlign: 'center', marginTop: 13 }, resultSummary: { color: colors.muted, textAlign: 'center', fontSize: 13, lineHeight: 20, marginTop: 6 }, resultLabel: { color: colors.red, fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.2, marginTop: 22, marginBottom: 8 }, matchRow: { minHeight: 64, borderRadius: 14, backgroundColor: colors.canvas, padding: 12, flexDirection: 'row', alignItems: 'center', marginBottom: 8 }, matchName: { fontFamily: fonts.bold, fontSize: 14 }, matchMeta: { color: colors.muted, fontSize: 11, marginTop: 4 }, bullet: { flexDirection: 'row', alignItems: 'flex-start', gap: 8, marginVertical: 5 }, bulletText: { flex: 1, color: colors.muted, fontSize: 12, lineHeight: 18 },
  checkRow: { minHeight: 74, borderRadius: 16, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.line, padding: 12, flexDirection: 'row', alignItems: 'center' }, checkBox: { width: 42, height: 42, borderRadius: 13, backgroundColor: colors.canvas, borderWidth: 1, borderColor: colors.line, alignItems: 'center', justifyContent: 'center', marginRight: 12 }, checkBoxChecked: { backgroundColor: colors.success, borderColor: colors.success }, checkTitle: { fontFamily: fonts.bold, fontSize: 13 }, checkHint: { color: colors.muted, fontSize: 10, lineHeight: 15, marginTop: 3 },
  serviceIcon: { width: 52, height: 52, borderRadius: 16, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 12 }, dateTile: { width: 52, height: 52, borderRadius: 16, backgroundColor: colors.redSoft, alignItems: 'center', justifyContent: 'center', marginRight: 12 }, miniCard: { width: 188, minHeight: 160, borderRadius: 18, borderWidth: 1, borderColor: colors.line, backgroundColor: colors.white, padding: 15, ...shadows.card }, miniTitle: { fontFamily: fonts.bold, fontSize: 15, marginTop: 15 }, miniCopy: { color: colors.muted, fontSize: 11, lineHeight: 17, marginTop: 6 }, contactCard: { minHeight: 82, borderRadius: 18, backgroundColor: colors.softBlue, padding: 16, flexDirection: 'row', alignItems: 'center', gap: 12, marginTop: 28 }, contactTitle: { fontFamily: fonts.bold, fontSize: 15 }, contactCopy: { color: colors.muted, fontSize: 12, marginTop: 4 },
});

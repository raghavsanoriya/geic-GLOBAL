import React,{useRef,useState} from 'react';
import {ActivityIndicator,KeyboardAvoidingView,Platform,ScrollView,Share,View} from 'react-native';
import {useNavigation,type RouteProp} from '@react-navigation/native';
import {enquire,request} from './native-api';
import {canRegister,localDate,text,validateEnquiry,type Enquiry,type RecordData} from './native-core';
import {Button,Card,Check,Chips,ErrorNote,Field,Fields,Heading,Screen,T,colors,s,useApp,type Nav,type Routes} from './native-ui';
const levels=['Undergraduate','Postgraduate','Diploma or pathway','Research'];
const tests=['IELTS','PTE','TOEFL','Duolingo','Planning to take a test','Not sure yet'];
export function BookingScreen({route}:{route:RouteProp<Routes,'Booking'>}){
 const app=useApp();const [form,setForm]=useState<Enquiry>({kind:route.params?.kind||'counselling',referenceId:route.params?.referenceId,fullName:'',phone:'',email:'',consent:false,destination:route.params?.destination||'Undecided / Multiple',studyLevel:'Postgraduate',appointmentDate:localDate(),timeSlot:'11:30 AM',meetingMode:'Indore Office (In-Person)'});
 const [pending,setPending]=useState(false),[error,setError]=useState(''),[reference,setReference]=useState('');
 const inFlight=useRef(false);
 const set=(key:keyof Enquiry,value:string|boolean)=>setForm(prev=>({...prev,[key]:value}));
 const event=app.data.UPCOMING_EVENTS_LIST.find(e=>String(e.id)===form.referenceId);
 const blocked=['event','expo'].includes(form.kind)&&(!event||!canRegister(event));
 async function submit(){
   if(inFlight.current)return;
   const problem=validateEnquiry(form,localDate());
   if(problem){setError(problem);return;}
   if(blocked){setError('Registration is closed for this event.');return;}
   inFlight.current=true;setPending(true);setError('');
   try{const result=await enquire(form);setReference(result.reference);}
   catch(e){setError(e instanceof Error?e.message:'Could not send request.');}
   finally{inFlight.current=false;setPending(false);}
 }
 if(reference)return <Screen><Heading title="Request Received!" copy="Your counsellor will confirm the appointment or event registration. No slot is confirmed yet."/><Card><T style={s.heading}>{reference}</T><Fields value={{fullName:form.fullName,destination:form.destination||null,studyLevel:form.studyLevel||null,date:form.appointmentDate||null,timeSlot:form.timeSlot||null,mode:form.meetingMode||null}}/></Card><Button label="Share request reference" onPress={()=>void Share.share({message:'GEIC request '+reference+'\nAwaiting counsellor confirmation.'}).catch(()=>setError('Sharing is unavailable.'))}/><ErrorNote message={error}/></Screen>;
 return <KeyboardAvoidingView style={{flex:1}} behavior={Platform.OS==='ios'?'padding':undefined}><Screen><Heading title={form.kind==='event'?'Request Event Registration':'Book Free Counselling'} copy="Tell us your preferences. A GEIC counsellor will contact you to confirm."/><ErrorNote message={error}/>{blocked&&<ErrorNote message="Registration is unavailable for this event."/>}
 <Card><T style={s.label}>Consultation Preference</T><Chips items={['Indore Office (In-Person)','Online (Video Call)']} selected={form.meetingMode||''} onSelect={v=>set('meetingMode',v)}/>
 <T style={s.label}>Destination</T><Chips items={['Undecided / Multiple',...app.data.STUDY_DESTINATIONS.map(d=>text(d.name))]} selected={form.destination||''} onSelect={v=>set('destination',v)}/>
 <T style={s.label}>Study Level</T><Chips items={levels} selected={form.studyLevel||''} onSelect={v=>set('studyLevel',v)}/>
 <Field label="Your Full Name *" value={form.fullName} onChangeText={v=>set('fullName',v)} autoComplete="name" maxLength={120}/>
 <Field label="Phone / WhatsApp *" value={form.phone} onChangeText={v=>set('phone',v)} keyboardType="phone-pad" autoComplete="tel" maxLength={24}/>
 <Field label="Email Address (optional)" value={form.email} onChangeText={v=>set('email',v)} autoCapitalize="none" keyboardType="email-address" autoComplete="email" maxLength={160}/>
 <Field label="City" value={form.city||''} onChangeText={v=>set('city',v)} maxLength={100}/>
 <Field label="Preferred Date (YYYY-MM-DD)" value={form.appointmentDate} onChangeText={v=>set('appointmentDate',v)} maxLength={10}/>
 <T style={s.label}>Preferred Time (IST) — subject to confirmation</T><Chips items={['10:30 AM','11:30 AM','1:00 PM','2:30 PM','4:00 PM','5:30 PM']} selected={form.timeSlot||''} onSelect={v=>set('timeSlot',v)}/>
 <Field label="Preferred Intake" value={form.preferredIntake||''} onChangeText={v=>set('preferredIntake',v)} maxLength={50}/>
 <Field label="Preferred Course" value={form.preferredCourse||''} onChangeText={v=>set('preferredCourse',v)} maxLength={160}/>
 <T style={s.label}>English Test</T><Chips items={tests} selected={form.englishTest||''} onSelect={v=>set('englishTest',v)}/>
 <Field label="Message / Notes" value={form.message||''} onChangeText={v=>set('message',v)} multiline maxLength={1200}/>
 <Check label="I agree to be contacted by a GEIC counsellor about this request." value={form.consent} onPress={()=>set('consent',!form.consent)}/>
 <Button label={pending?'Sending request…':'Send Counselling Request'} disabled={pending||blocked} onPress={()=>void submit()}/></Card></Screen></KeyboardAvoidingView>;
}
export function AdvisorScreen(){
 const nav=useNavigation<Nav>();const [messages,setMessages]=useState<{role:'user'|'assistant';content:string}[]>([]),[draft,setDraft]=useState(''),[pending,setPending]=useState(false),[error,setError]=useState(''),[source,setSource]=useState('');
 const flight=useRef(false),scroll=useRef<ScrollView>(null);
 async function send(question=draft){if(!question.trim()||flight.current)return;flight.current=true;setPending(true);setError('');const message=question.trim();const history=messages.slice(-10).map(m=>({...m,content:m.content.slice(0,2000)}));
 try{const response=await request<{reply:string;source:string}>('study-assistant/chat',{message,history});setMessages(prev=>[...prev,{role:'user',content:message},{role:'assistant',content:response.reply}]);setDraft('');setSource(response.source);}
 catch(e){setError(e instanceof Error?e.message:'Advisor unavailable.');}
 finally{flight.current=false;setPending(false);}
 }
 return <KeyboardAvoidingView style={s.screen} behavior={Platform.OS==='ios'?'padding':undefined} keyboardVerticalOffset={90}><View style={{padding:16,gap:10}}><Heading title="Trans Globe Study Advisor" copy={source==='assistant'?'AI-assisted study guidance':'GEIC catalogue-guided study planning'}/><Button label="Talk to a counsellor" secondary onPress={()=>nav.navigate('Booking')}/></View><ScrollView ref={scroll} onContentSizeChange={()=>scroll.current?.scrollToEnd({animated:false})} contentContainerStyle={{padding:16,gap:14}} keyboardShouldPersistTaps="handled"><Card><T>Share your preferred destination, course, academic result or intake. I can help you explore the GEIC catalogue.</T></Card>{messages.map((m,i)=><Card key={i} style={{backgroundColor:m.role==='user'?'#fef2f2':'#fff',alignSelf:m.role==='user'?'flex-end':'flex-start',maxWidth:'95%'}}><T style={s.label}>{m.role==='user'?'You':'GEIC Advisor'}</T><T selectable>{m.content}</T></Card>)}{pending&&<ActivityIndicator color={colors.red}/>}<ErrorNote message={error}/></ScrollView><View style={{padding:12,gap:10,borderTopWidth:1,borderTopColor:colors.line}}><Chips items={['Compare Australia and the UK','Which test should I take?','Scholarship guidance']} selected="" onSelect={q=>void send(q)}/><Field label="Ask your study-abroad question" value={draft} onChangeText={setDraft} multiline maxLength={1200}/><Button label={pending?'Getting guidance…':'Send message'} disabled={pending||!draft.trim()} onPress={()=>void send()}/></View></KeyboardAvoidingView>;
}
export function EvaluateScreen(){
 const app=useApp(),nav=useNavigation<Nav>();const [name,setName]=useState(''),[score,setScore]=useState(''),[level,setLevel]=useState('Postgraduate'),[countries,setCountries]=useState<string[]>([]),[course,setCourse]=useState(''),[test,setTest]=useState('Planning to take a test'),[testScore,setTestScore]=useState(''),[experience,setExperience]=useState('0'),[intake,setIntake]=useState('');
 const [pending,setPending]=useState(false),[error,setError]=useState(''),[result,setResult]=useState<RecordData|null>(null);const flight=useRef(false);
 async function evaluate(){if(flight.current)return;const n=Number(score);if(!score.trim()||!Number.isFinite(n)||n<35||n>100){setError('Enter an academic percentage between 35 and 100.');return;}if(!countries.length){setError('Choose at least one target country.');return;}flight.current=true;setPending(true);setError('');
 try{setResult(await request<RecordData>('profile-evaluations',{fullName:name,academicPercentage:n,studyLevel:level,preferredDestinations:countries,intendedCourse:course,englishTest:test,englishScore:testScore,workExperienceYears:Number(experience),preferredIntake:intake}));}
 catch(e){setError(e instanceof Error?e.message:'Evaluation unavailable.');}finally{flight.current=false;setPending(false);}}
 return <Screen><Heading title="Profile Evaluator & Visa Meter" copy="Review application readiness—not a prediction of admission or visa approval."/><Card><Field label="Full Name" value={name} onChangeText={setName} maxLength={120}/><Field label="Academic Score / GPA converted to Percentage *" value={score} onChangeText={setScore} keyboardType="decimal-pad"/>
 <T style={s.label}>Intended Study Level</T><Chips items={levels} selected={level} onSelect={setLevel}/><T style={s.label}>Target Countries *</T><Chips items={app.data.STUDY_DESTINATIONS.map(d=>text(d.name))} selected={app.data.STUDY_DESTINATIONS.filter(d=>countries.includes(String(d.id))).map(d=>text(d.name))} onSelect={v=>{const id=String(app.data.STUDY_DESTINATIONS.find(d=>d.name===v)?.id);setCountries(prev=>prev.includes(id)?prev.filter(x=>x!==id):[...prev,id]);}}/>
 <Field label="Field of Study" value={course} onChangeText={setCourse} maxLength={160}/><T style={s.label}>Language Exam</T><Chips items={tests} selected={test} onSelect={setTest}/><Field label="Band / Score" value={testScore} onChangeText={setTestScore} maxLength={30}/><Field label="Work Experience (years)" value={experience} onChangeText={setExperience} keyboardType="decimal-pad"/><Field label="Preferred Intake" value={intake} onChangeText={setIntake} maxLength={80}/><ErrorNote message={error}/><Button label={pending?'Checking…':'Check application readiness'} disabled={pending} onPress={()=>void evaluate()}/></Card>
 {result&&<Card><Heading title={'Application readiness: '+text(result.readinessScore)+' / 100'}/><Fields value={result} omit={['readinessScore']}/><Button label="Discuss Report with a Specialist" onPress={()=>nav.navigate('Booking')}/></Card>}
 <Button label="Interactive Visa Process Roadmap" secondary onPress={()=>nav.navigate('Visa')}/><Heading title="Application Document Vault" copy="A local preparation checklist, not document uploads."/>
 <Card>{['Valid Passport','10th, 12th & Degree Marksheets','Statement of Purpose (SOP)','Letters of Recommendation (LOR)','English Test Scorecard','Proof of Funds / Bank Statement'].map(doc=><Check key={doc} label={doc} value={app.checks.includes('vault:'+doc)} onPress={()=>app.toggleCheck('vault:'+doc)}/>)}</Card></Screen>;
}

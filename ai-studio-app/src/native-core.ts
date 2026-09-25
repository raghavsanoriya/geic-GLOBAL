export type Value = string | number | boolean | null | Value[] | { [key: string]: Value };
export type RecordData = { [key: string]: Value };
export type Collection = 'STUDY_DESTINATIONS' | 'GLOBAL_UNIVERSITIES' | 'GEIC_SERVICES' | 'ENGLISH_TESTS' | 'UPCOMING_EVENTS_LIST' | 'GEIC_BLOGS' | 'STUDENT_REVIEWS' | 'NOTIFICATIONS';
export type Catalog = {
  schemaVersion: number; source: string; loadedAt: string;
  GEIC_BRAND: RecordData; ABOUT_INFO: RecordData; UPCOMING_EXPO: RecordData;
  GEIC_STATS: RecordData[]; HERO_SLIDES: RecordData[]; FOUR_STEPS: RecordData[]; FAQS: RecordData[];
  UNIVERSITY_PARTNERS: RecordData[]; VISA_ROADMAP_DATA: Record<string, RecordData>;
} & Record<Collection, RecordData[]>;
export const collections: {key: Collection; title: string; icon: string}[] = [
  {key:'STUDY_DESTINATIONS',title:'Study Destinations',icon:'earth'},
  {key:'GLOBAL_UNIVERSITIES',title:'Universities',icon:'school-outline'},
  {key:'GEIC_SERVICES',title:'Services',icon:'briefcase-outline'},
  {key:'ENGLISH_TESTS',title:'Test Preparation',icon:'book-open-outline'},
  {key:'UPCOMING_EVENTS_LIST',title:'Events & Workshops',icon:'calendar-outline'},
  {key:'GEIC_BLOGS',title:'Study Abroad Guides & News',icon:'newspaper-variant-outline'},
  {key:'STUDENT_REVIEWS',title:'Student Reviews',icon:'star-outline'},
  {key:'NOTIFICATIONS',title:'Notifications',icon:'bell-outline'},
];
export function text(value: Value | undefined): string {
  if (value === null || value === undefined || value === '') return 'Not provided';
  if (typeof value === 'boolean') return value ? 'Yes' : 'No';
  if (Array.isArray(value)) return value.map(text).join(', ');
  if (typeof value === 'object') return Object.values(value).map(text).join(' · ');
  return String(value);
}
export function title(item: RecordData): string { return text(item.title || item.name || item.studentName || item.country); }
export function label(key: string): string { return key.replace(/([a-z])([A-Z])/g,'$1 $2').replace(/_/g,' ').replace(/^./, s=>s.toUpperCase()); }
export function recordKey(group: Collection, item: RecordData): string { return group+':'+String(item.id); }
export function filterRecords(items: RecordData[], query: string, country = 'All'): RecordData[] {
  const words=query.toLowerCase().trim().split(/\s+/).filter(Boolean);
  return items.filter(item=>(country==='All' || item.country===country || item.name===country) && words.every(word=>JSON.stringify(item).toLowerCase().includes(word)));
}
export function validateCatalog(value: unknown): Catalog {
  const c=value as Catalog;
  const record=(v:unknown):v is RecordData=>Boolean(v)&&typeof v==='object'&&!Array.isArray(v);
  const recordList=(v:unknown)=>Array.isArray(v)&&v.every(record);
  if(!record(c) || c.schemaVersion!==1 || c.source!=='laravel' || collections.some(g=>!recordList(c[g.key]) || c[g.key].some(r=>typeof r.id!=='string'&&typeof r.id!=='number')) || !record(c.GEIC_BRAND) || !record(c.ABOUT_INFO) || !record(c.UPCOMING_EXPO) || !record(c.VISA_ROADMAP_DATA) || !Object.values(c.VISA_ROADMAP_DATA).every(record) || !recordList(c.HERO_SLIDES) || !recordList(c.FOUR_STEPS) || !recordList(c.GEIC_STATS) || !recordList(c.FAQS) || !recordList(c.UNIVERSITY_PARTNERS)) {
    throw new Error('The mobile catalogue is not available on this server yet. Please retry or contact GEIC.');
  }
  return c;
}
export function assetUrl(path: Value | undefined, origin: string): string | undefined {
  if(typeof path!=='string' || !path.trim()) return undefined;
  try { const url=new URL(path,origin); return ['http:','https:'].includes(url.protocol)?url.toString():undefined; } catch { return undefined; }
}
export function canRegister(item: RecordData): boolean { return item.registrationOpen === true; }
export type Enquiry = {
  kind:'counselling'|'event'|'service'|'expo'; fullName:string; phone:string; email:string; consent:boolean;
  destination?:string; studyLevel?:string; city?:string; preferredIntake?:string; preferredCourse?:string;
  englishTest?:string; message?:string; referenceId?:string; appointmentDate?:string; timeSlot?:string; meetingMode?:string;
};
export function validateEnquiry(input: Enquiry, today: string): string | undefined {
  if(!input.fullName.trim()) return 'Enter your full name.';
  if(!/^[0-9+()\-\s]+$/.test(input.phone) || input.phone.replace(/\D/g,'').length<7 || input.phone.replace(/\D/g,'').length>15) return 'Enter a valid phone number.';
  if(input.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.email)) return 'Enter a valid email address.';
  if(input.appointmentDate && (!/^\d{4}-\d{2}-\d{2}$/.test(input.appointmentDate) || (!Number.isFinite(Date.parse(input.appointmentDate)) || new Date(input.appointmentDate).toISOString().slice(0,10)!==input.appointmentDate) || input.appointmentDate<today)) return 'Choose today or a future date (YYYY-MM-DD).';
  if(!input.consent) return 'Please allow a GEIC counsellor to contact you.';
}
export function localDate(date=new Date()): string { return [date.getFullYear(),String(date.getMonth()+1).padStart(2,'0'),String(date.getDate()).padStart(2,'0')].join('-'); }

export function isNativeAppLink(url:string):boolean { return /^(tel:|mailto:|whatsapp:|geo:|maps:)/i.test(url); }

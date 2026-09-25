import {test} from 'node:test';
import assert from 'node:assert/strict';
import {isNativeAppLink,assetUrl,canRegister,collections,filterRecords,localDate,recordKey,validateCatalog,validateEnquiry,type Enquiry} from './native-core.ts';
const booking:Enquiry={kind:'counselling',fullName:'Test Student',phone:'+91 90000 00000',email:'student@example.com',consent:true,appointmentDate:'2026-10-12'};
test('valid counselling input retains optional email and date',()=>{assert.equal(validateEnquiry(booking,'2026-09-25'),undefined);assert.equal(validateEnquiry({...booking,email:''},'2026-09-25'),undefined);});
for(const [name,patch] of Object.entries({name:{fullName:''},phone:{phone:'-------'},email:{email:'invalid'},consent:{consent:false},past:{appointmentDate:'2026-09-24'},date:{appointmentDate:'2027-02-30'}})){
 test('rejects invalid '+name,()=>assert.ok(validateEnquiry({...booking,...patch},'2026-09-25')));
}
test('local dates avoid UTC day shifting',()=>assert.equal(localDate(new Date(2026,8,25,0,1)),'2026-09-25'));
test('search and country filters use live record values',()=>{const rows=[{id:'a',name:'Example University',country:'Australia'},{id:'b',name:'Example College',country:'Canada'}];assert.deepEqual(filterRecords(rows,'EXAMPLE university','Australia'),[rows[0]]);assert.deepEqual(filterRecords(rows,'university','Canada'),[]);});
test('bookmark identity does not collide across collections',()=>assert.notEqual(recordKey('GEIC_SERVICES',{id:'test'}),recordKey('ENGLISH_TESTS',{id:'test'})));
test('assets resolve through API origin and reject executable URLs',()=>{assert.equal(assetUrl('/assets/logo.png','https://www.geic.in'),'https://www.geic.in/assets/logo.png');assert.equal(assetUrl('javascript:alert(1)','https://www.geic.in'),undefined);assert.equal(assetUrl('','https://www.geic.in'),undefined);});
test('event registration requires explicit availability',()=>{assert.equal(canRegister({registrationOpen:true}),true);assert.equal(canRegister({registrationOpen:false}),false);assert.equal(canRegister({}),false);});
test('missing API contract fails clearly instead of showing mock data',()=>{assert.throws(()=>validateCatalog({data:{}}),/not available/);assert.throws(()=>validateCatalog('<html>404</html>'),/not available/);});
test('live local Laravel contract covers all native directories',async()=>{const response=await fetch((process.env.TEST_API_ORIGIN || 'http://127.0.0.1:8085')+'/api/mobile/catalog');assert.equal(response.status,200);const body=await response.json();const c=validateCatalog(body.studio);for(const g of collections)assert.ok(Array.isArray(c[g.key]));assert.ok(c.STUDY_DESTINATIONS.length>0);assert.ok(c.GEIC_SERVICES.length>0);assert.equal(c.source,'laravel');});

test('native links never allow browser launches',()=>{for(const url of ['https://www.geic.in','http://localhost','javascript:alert(1)','file:///example'])assert.equal(isNativeAppLink(url),false);for(const url of ['tel:123','mailto:info@geic.in','whatsapp://send?phone=123','geo:0,0?q=Indore','maps:?q=Indore'])assert.equal(isNativeAppLink(url),true);});

const validContract={schemaVersion:1,source:'laravel',loadedAt:'2026-09-25',GEIC_BRAND:{},ABOUT_INFO:{},UPCOMING_EXPO:{},VISA_ROADMAP_DATA:{},HERO_SLIDES:[],FOUR_STEPS:[],GEIC_STATS:[],FAQS:[],UNIVERSITY_PARTNERS:[],...Object.fromEntries(collections.map(g=>[g.key,[]]))};
test('empty published collections remain valid without invented records',()=>assert.equal(validateCatalog(validContract).STUDENT_REVIEWS.length,0));
for(const [name,patch] of Object.entries({missingAbout:{ABOUT_INFO:null},invalidExpo:{UPCOMING_EXPO:[]},invalidPartner:{UNIVERSITY_PARTNERS:[null]},invalidRecord:{GLOBAL_UNIVERSITIES:[null]},missingIdentity:{GEIC_SERVICES:[{name:'Example'}]},invalidRoadmap:{VISA_ROADMAP_DATA:{Australia:null}}})){
 test('malformed catalogue rejects '+name,()=>assert.throws(()=>validateCatalog({...validContract,...patch}),/not available/));
}

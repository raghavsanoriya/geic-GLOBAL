import {test} from 'node:test';
import assert from 'node:assert/strict';
import {createPreferences,type Preferences} from './native-state.ts';
test('saved items, checklists and search history survive a restart',async()=>{
 let disk:string|null=null;
 const storage={getItem:async()=>disk,setItem:async(_key:string,value:string)=>{disk=value;}};
 const state=createPreferences(storage,()=>{},assert.fail);await state.load();
 await state.update(()=>({saved:['GEIC_SERVICES:1'],checks:['vault:Passport'],recent:['Canada']}));
 let restored:Preferences|undefined;
 await createPreferences(storage,value=>{restored=value;},assert.fail).load();
 assert.deepEqual(restored,{saved:['GEIC_SERVICES:1'],checks:['vault:Passport'],recent:['Canada']});
});
test('rapid changes are accumulated and disk writes stay ordered',async()=>{
 const writes:string[]=[];
 const storage={getItem:async()=>null,setItem:async(_key:string,value:string)=>{writes.push(value);}};
 const state=createPreferences(storage,()=>{},assert.fail);await state.load();
 const first=state.update(previous=>({...previous,saved:[...previous.saved,'first']}));
 const second=state.update(previous=>({...previous,saved:[...previous.saved,'second']}));
 await Promise.all([first,second]);
 assert.deepEqual(writes.map(s=>JSON.parse(s).saved),[['first'],['first','second']]);
});
for(const raw of ['{broken','null','{"saved":[42],"checks":[],"recent":[]}']){
 test('corrupted storage is recoverable: '+raw,async()=>{
  const errors:string[]=[];let disk='';
  const state=createPreferences({getItem:async()=>raw,setItem:async(_key,value)=>{disk=value;}},()=>{},message=>errors.push(message));
  await state.load();assert.equal(errors.length,1);
  await state.update(previous=>({...previous,saved:['new']}));
  assert.deepEqual(JSON.parse(disk).saved,['new']);
 });
}
test('a failed disk write is reported and does not block the next save',async()=>{
 let attempts=0;const errors:string[]=[];
 const state=createPreferences({getItem:async()=>null,setItem:async()=>{if(++attempts===1)throw new Error('disk');}},()=>{},m=>errors.push(m));
 await state.update(p=>({...p,saved:['one']}));await state.update(p=>({...p,saved:[...p.saved,'two']}));
 assert.equal(attempts,2);assert.equal(errors.length,1);
});

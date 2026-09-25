import {test} from 'node:test';
import assert from 'node:assert/strict';
import {mobileRequest} from './native-http.ts';
test('catalogue GET uses only the Laravel JSON endpoint',async t=>{
 t.mock.method(globalThis,'fetch',async (url:unknown,init:RequestInit)=>{
  assert.equal(url,'https://api.example/api/mobile/catalog');assert.equal(init.method,'GET');assert.equal(init.body,undefined);
  return Response.json({studio:{source:'laravel'}});
 });
 assert.deepEqual(await mobileRequest('https://api.example','catalog'),{studio:{source:'laravel'}});
});
test('enquiry POST preserves all supplied fields',async t=>{
 const payload={fullName:'QA Student',consent:true,referenceId:'service-1',appointmentDate:'2027-01-01'};
 t.mock.method(globalThis,'fetch',async (_url:unknown,init:RequestInit)=>{
  assert.equal(init.method,'POST');assert.deepEqual(JSON.parse(String(init.body)),payload);
  return Response.json({reference:'GEIC-42'},{status:201});
 });
 assert.deepEqual(await mobileRequest('https://api.example','enquiries',payload),{reference:'GEIC-42'});
});
for(const [name,status,body,expected] of [
 ['validation',422,{errors:{phone:['Enter a valid phone number.']}},/Enter a valid phone number/],
 ['rate limit',429,{message:'Too Many Attempts.'},/Too Many Attempts/],
 ['server failure',503,null,/unavailable/],
 ['malformed success',200,[],/invalid response/]
] as const){
 test(name+' is never treated as success',async t=>{
  t.mock.method(globalThis,'fetch',async()=>Response.json(body,{status}));
  await assert.rejects(mobileRequest('https://api.example','enquiries',{}),expected);
 });
}
test('HTML 404 from a missing deployment becomes a useful error',async t=>{
 t.mock.method(globalThis,'fetch',async()=>new Response('<html>404</html>',{status:404}));
 await assert.rejects(mobileRequest('https://api.example','catalog'),/invalid response/);
});
test('offline failure does not fabricate catalogue data',async t=>{
 t.mock.method(globalThis,'fetch',async()=>{throw new Error('Network request failed');});
 await assert.rejects(mobileRequest('https://api.example','catalog'),/Network request failed/);
});
test('a stalled connection times out',async t=>{
 t.mock.method(globalThis,'fetch',async (_url:unknown,init:RequestInit)=>new Promise((_resolve,reject)=>{
  init.signal?.addEventListener('abort',()=>reject(new DOMException('Aborted','AbortError')));
 }));
 await assert.rejects(mobileRequest('https://api.example','catalog',undefined,5),/timed out/);
});

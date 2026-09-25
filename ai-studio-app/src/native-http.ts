export async function mobileRequest<T>(origin:string,path:string,payload?:unknown,timeoutMs=30000):Promise<T> {
  const controller=new AbortController();
  const timeout=setTimeout(()=>controller.abort(),timeoutMs);
  try {
    const r=await fetch(origin+'/api/mobile/'+path,{method:payload===undefined?'GET':'POST',headers:{Accept:'application/json','Content-Type':'application/json'},body:payload===undefined?undefined:JSON.stringify(payload),signal:controller.signal});
    const body=await r.json().catch(()=>{throw new Error('GEIC returned an invalid response. Please try again.');});
    if(!r.ok) throw new Error((body && typeof body==='object' && (Object.values(body.errors || {}).flat().join('\n') || body.message)) || 'GEIC is unavailable. Please retry.');
    if(!body || typeof body!=='object' || Array.isArray(body)) throw new Error('GEIC returned an invalid response. Please try again.');
    return body as T;
  } catch(error) { if(error instanceof Error && error.name==='AbortError') throw new Error('The connection timed out. Please try again.'); throw error; }
  finally { clearTimeout(timeout); }
}

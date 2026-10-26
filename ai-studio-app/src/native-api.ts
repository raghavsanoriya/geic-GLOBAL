import { mobileRequest } from './native-http';
import { Platform } from 'react-native';
import { validateCatalog, type Catalog, type Enquiry } from './native-core';
export const origin=(process.env.EXPO_PUBLIC_LARAVEL_URL || 'https://www.geic.in').replace(/\/$/,'');
export const request=<T>(path:string,payload?:unknown):Promise<T>=>mobileRequest<T>(origin,path,payload);
export async function catalog():Promise<Catalog> {
  const r=await request<{studio:unknown}>('catalog'); return validateCatalog(r.studio);
}
export async function enquire(input:Enquiry):Promise<{reference:string;message:string}> {
  const r=await request<{reference:string;message:string}>('enquiries',{...input,email:input.email.trim() || 'no-email-'+Date.now()+'@example.invalid',emailProvided:Boolean(input.email.trim()),platform:Platform.OS});
  if(!r.reference) throw new Error('Your request was not confirmed. Please retry.');
  return r;
}


export type Preferences={saved:string[];checks:string[];recent:string[]};
const empty=():Preferences=>({saved:[],checks:[],recent:[]});
export function createPreferences(storage:{getItem:(key:string)=>Promise<string|null>;setItem:(key:string,value:string)=>Promise<unknown>},changed:(v:Preferences)=>void,failed:(message:string)=>void) {
  const key='tranglobe-geic-native-v2';
  let current=empty(),writes:Promise<unknown>=Promise.resolve();
  return {
    async load() {
      try {
        const raw=await storage.getItem(key);
        if(raw) {
          const value=JSON.parse(raw);
          if(!value || !['saved','checks','recent'].every(k=>Array.isArray(value[k])&&value[k].every((x:unknown)=>typeof x==='string'))) throw new Error('Invalid preferences');
          current={saved:value.saved,checks:value.checks,recent:value.recent.slice(0,8)};
          changed(current);
        }
      } catch {failed('Saved preferences could not be read. New selections can still be made.');}
    },
    update(change:(previous:Preferences)=>Preferences) {
      current=change(current);
      changed(current);
      const encoded=JSON.stringify(current);
      writes=writes.then(()=>storage.setItem(key,encoded)).catch(()=>failed('Could not save your selections on this device.'));
      return writes;
    }
  };
}

import React, { createContext, useContext } from 'react';
import { ActivityIndicator, Alert, Image, Linking, Pressable, ScrollView, StyleSheet, Text, TextInput, View, type TextInputProps, type StyleProp, type TextStyle, type ViewStyle } from 'react-native';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { NavigatorScreenParams } from '@react-navigation/native';
import { origin } from './native-api';
import { isNativeAppLink, assetUrl, label, text, title, type Catalog, type Collection, type RecordData, type Value } from './native-core';

export type Tabs={Home:undefined;Explore:undefined;Advisor:undefined;Evaluate:undefined;More:undefined};
export type Routes={Main:NavigatorScreenParams<Tabs>|undefined;Directory:{collection:Collection};Detail:{collection:Collection;id:string};Booking:{kind?:'counselling'|'service'|'event'|'expo';referenceId?:string;destination?:string}|undefined;Compare:undefined;Visa:{country?:string}|undefined;About:undefined;Contact:undefined;Saved:undefined;Menu:undefined;Search:undefined};
export type Nav=NativeStackNavigationProp<Routes>;
export type AppState={data:Catalog;saved:string[];toggleSave:(id:string)=>void;checks:string[];toggleCheck:(id:string)=>void;recent:string[];remember:(value:string)=>void;clearRecent:()=>void;refresh:()=>Promise<void>;refreshing:boolean;error:string;storageError:string};
export const AppContext=createContext<AppState|null>(null);
export function useApp(){const value=useContext(AppContext);if(!value)throw new Error('App data unavailable');return value;}
export const colors={red:'#dc2626',navy:'#0f172a',muted:'#64748b',line:'#e2e8f0',canvas:'#f8fafc',white:'#fff'};
export function T({children,style,...rest}:{children:React.ReactNode;style?:StyleProp<TextStyle>;numberOfLines?:number;selectable?:boolean}) {return <Text {...rest} style={[s.text,style]}>{children}</Text>;}
export function Icon({name,color=colors.navy,size=22}:{name:string;color?:string;size?:number}){return <MaterialCommunityIcons name={name as React.ComponentProps<typeof MaterialCommunityIcons>['name']} color={color} size={size}/>;}
export function Button({label,onPress,secondary=false,disabled=false,icon,style}:{label:string;onPress:()=>void;secondary?:boolean;disabled?:boolean;icon?:string;style?:StyleProp<ViewStyle>}) {
 return <Pressable accessibilityRole="button" accessibilityLabel={label} accessibilityState={{disabled}} disabled={disabled} onPress={onPress} style={({pressed})=>[s.button,secondary&&s.secondary,disabled&&{opacity:.5},pressed&&{opacity:.75},style]}>{icon&&<Icon name={icon} color={secondary?colors.navy:'#fff'} size={18}/>}<T style={[s.buttonText,secondary&&{color:colors.navy}]}>{label}</T></Pressable>;
}
export function IconButton({name,label,onPress}:{name:string;label:string;onPress:()=>void}) {return <Pressable accessibilityRole="button" accessibilityLabel={label} onPress={onPress} style={({pressed})=>[s.iconButton,pressed&&{opacity:.6}]}><Icon name={name}/></Pressable>;}
export function Field({label:caption,...props}:TextInputProps&{label:string}){return <View style={{gap:6}}><T style={s.label}>{caption}</T><TextInput accessibilityLabel={caption} placeholderTextColor={colors.muted} {...props} style={[s.input,props.multiline&&{minHeight:100,textAlignVertical:'top'},props.style]}/></View>;}
export function Chips({items,selected,onSelect}:{items:string[];selected:string|string[];onSelect:(value:string)=>void}) {return <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={s.chips} keyboardShouldPersistTaps="handled">{items.map(item=>{const active=Array.isArray(selected)?selected.includes(item):selected===item;return <Pressable key={item} accessibilityRole="button" accessibilityState={{selected:active}} onPress={()=>onSelect(item)} style={[s.chip,active&&s.chipOn]}><T style={[s.chipText,active&&{color:'#fff'}]}>{item}</T></Pressable>;})}</ScrollView>;}
export function Screen({children}:{children:React.ReactNode}) {return <ScrollView style={s.screen} keyboardShouldPersistTaps="handled" contentContainerStyle={s.scroll}>{children}</ScrollView>;}
export function Heading({title,eyebrow,copy}:{title:string;eyebrow?:string;copy?:string}) {return <View style={{gap:5}}>{eyebrow&&<T style={s.eyebrow}>{eyebrow}</T>}<T style={s.heading}>{title}</T>{copy&&<T style={s.muted}>{copy}</T>}</View>;}
export function Card({children,style}:{children:React.ReactNode;style?:StyleProp<ViewStyle>}) {return <View style={[s.card,style]}>{children}</View>;}
export function ErrorNote({message}:{message:string}) {return message?<View accessibilityRole="alert" style={s.error}><T style={{color:'#991b1b'}}>{message}</T></View>:null;}
export function Empty({message='No published information is available yet.'}:{message?:string}) {return <Card><Icon name="information-outline"/><T style={s.muted}>{message}</T></Card>;}
export function Photo({value,height=150}:{value:Value|undefined;height?:number}){const uri=assetUrl(value,origin);return uri?<Image accessibilityLabel="Catalogue image" source={{uri}} style={{width:'100%',height,borderRadius:12}} resizeMode="cover"/>:null;}
export function Check({label:caption,value,onPress}:{label:string;value:boolean;onPress:()=>void}) {return <Pressable accessibilityRole="checkbox" accessibilityLabel={caption} accessibilityState={{checked:value}} onPress={onPress} style={s.check}><Icon name={value?'checkbox-marked':'checkbox-blank-outline'} color={value?colors.red:colors.muted}/><T style={{flex:1}}>{caption}</T></Pressable>;}
export function Fields({value,omit=[]}:{value:RecordData;omit?:string[]}) {
 return <View style={{gap:14}}>{Object.entries(value).filter(([key])=>!omit.includes(key)).map(([key,item])=><View key={key} style={{gap:6}}><T style={s.label}>{label(key)}</T><ValueView value={item}/></View>)}</View>;
}
function ValueView({value}:{value:Value}) {
 if(Array.isArray(value)) return value.length?<View style={{gap:10}}>{value.map((entry,i)=><View key={i} style={{gap:8}}>{typeof entry==='object' && entry!==null && !Array.isArray(entry)?<Fields value={entry}/>:<T selectable>{text(entry)}</T>}</View>)}</View>:<T style={s.muted}>Not provided</T>;
 if(value && typeof value==='object') return <Fields value={value}/>;
 return <T selectable>{text(value)}</T>;
}
export function ItemCard({item,onPress,onSave,saved,compact=false}:{item:RecordData;onPress:()=>void;onSave?:()=>void;saved?:boolean;compact?:boolean}) {
 return <View style={[s.card,compact&&{width:260}]}><Pressable accessibilityRole="button" accessibilityLabel={'View '+title(item)} onPress={onPress} style={{gap:10}}><Photo value={item.imageUrl||item.bannerImage||item.bannerUrl||item.coverImage||item.logoUrl} height={compact?120:145}/><T style={s.title}>{title(item)}</T><T numberOfLines={3} style={s.muted}>{text(item.tagline||item.shortDesc||item.subtitle||item.country||item.excerpt||item.comment||item.summary)}</T>{item.date&&<T style={s.label}>{text(item.date)} · {text(item.badge)}</T>}</Pressable><View style={s.row}><Button label="View details" secondary onPress={onPress} style={{flex:1}}/>{onSave&&<IconButton name={saved?'bookmark':'bookmark-outline'} label={saved?'Remove saved item':'Save item'} onPress={onSave}/>}</View></View>;
}
export async function openExternal(url:string){try{if(!isNativeAppLink(url))throw new Error('Unsupported link');await Linking.openURL(url);}catch{Alert.alert('Could not open link','The required native app is unavailable. No browser page was opened.');}}
export const s=StyleSheet.create({
 text:{fontFamily:'Jakarta',fontSize:14,lineHeight:21,color:colors.navy},
 screen:{flex:1,backgroundColor:colors.canvas},
 scroll:{padding:16,gap:18,paddingBottom:32},
 heading:{fontFamily:'JakartaExtra',fontSize:23,lineHeight:30},
 title:{fontFamily:'JakartaBold',fontSize:16,lineHeight:23},
 label:{fontFamily:'JakartaBold',fontSize:12,lineHeight:18},
 eyebrow:{fontFamily:'JakartaBold',fontSize:11,letterSpacing:1,color:colors.red,textTransform:'uppercase'},
 muted:{color:colors.muted,fontSize:13,lineHeight:20},
 card:{backgroundColor:'#fff',padding:16,borderWidth:1,borderColor:colors.line,borderRadius:16,gap:12},
 button:{minHeight:48,paddingVertical:12,paddingHorizontal:16,backgroundColor:colors.red,borderRadius:12,alignItems:'center',justifyContent:'center',flexDirection:'row',gap:8},
 secondary:{backgroundColor:'#f1f5f9',borderWidth:1,borderColor:colors.line},
 buttonText:{fontFamily:'JakartaBold',color:'#fff',fontSize:13,textAlign:'center'},
 iconButton:{width:48,height:48,borderRadius:12,backgroundColor:'#f1f5f9',alignItems:'center',justifyContent:'center'},
 input:{fontFamily:'Jakarta',fontSize:16,minHeight:48,borderWidth:1,borderColor:'#cbd5e1',borderRadius:10,padding:12,color:colors.navy,backgroundColor:'#fff'},
 chips:{gap:8,paddingVertical:4},chip:{minHeight:44,paddingHorizontal:14,paddingVertical:10,borderWidth:1,borderColor:colors.line,borderRadius:10,justifyContent:'center',backgroundColor:'#fff'},
 chipOn:{backgroundColor:colors.navy,borderColor:colors.navy},chipText:{fontSize:12,fontFamily:'JakartaBold'},
 row:{flexDirection:'row',gap:10,alignItems:'center'},check:{flexDirection:'row',gap:12,minHeight:48,alignItems:'center',paddingVertical:8},
 error:{padding:14,borderRadius:12,backgroundColor:'#fef2f2',borderWidth:1,borderColor:'#fecaca'},
 hero:{padding:18,minHeight:260,justifyContent:'flex-end',gap:10,backgroundColor:colors.navy,borderRadius:18,overflow:'hidden'},
 heroTitle:{fontFamily:'JakartaExtra',fontSize:29,lineHeight:36,color:'#fff'},
 grid:{flexDirection:'row',flexWrap:'wrap',gap:12},half:{width:'48%',flexGrow:1},
});

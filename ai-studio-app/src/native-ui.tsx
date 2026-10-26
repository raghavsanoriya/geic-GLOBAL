import React, { createContext, useContext, useEffect, useRef } from 'react';
import { AccessibilityInfo, ActivityIndicator, Alert, Animated, Easing, Image, ImageBackground, Linking, Pressable, ScrollView, StyleSheet, Text, TextInput, View, type TextInputProps, type StyleProp, type TextStyle, type ViewStyle } from 'react-native';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { NavigatorScreenParams } from '@react-navigation/native';
import { origin } from './native-api';
import { isNativeAppLink, assetUrl, label, text, title, type Catalog, type Collection, type RecordData, type Value } from './native-core';

export type Tabs={Home:undefined;Explore:undefined;Advisor:undefined;Evaluate:undefined;More:undefined};
export type Routes={Main:NavigatorScreenParams<Tabs>|undefined;Directory:{collection:Collection};Destination:{id:string};Detail:{collection:Collection;id:string};Booking:{kind?:'counselling'|'service'|'event'|'expo';referenceId?:string;destination?:string}|undefined;Compare:undefined;Visa:{country?:string}|undefined;About:undefined;Contact:undefined;Notifications:undefined;Saved:undefined;Menu:undefined;Search:undefined};
export type Nav=NativeStackNavigationProp<Routes>;
export type AppState={data:Catalog;saved:string[];toggleSave:(id:string)=>void;checks:string[];toggleCheck:(id:string)=>void;recent:string[];remember:(value:string)=>void;clearRecent:()=>void;refresh:()=>Promise<void>;refreshing:boolean;error:string;storageError:string};
export const AppContext=createContext<AppState|null>(null);
export function useApp(){const value=useContext(AppContext);if(!value)throw new Error('App data unavailable');return value;}
export const colors={red:'#e11d48',navy:'#0f172a',muted:'#64748b',line:'#e7e5e4',canvas:'#f8fafc',white:'#fff'};
export function T({children,style,...rest}:{children:React.ReactNode;style?:StyleProp<TextStyle>;numberOfLines?:number;selectable?:boolean}) {return <Text {...rest} style={[s.text,style]}>{children}</Text>;}
export function Icon({name,color=colors.navy,size=22}:{name:string;color?:string;size?:number}){return <MaterialCommunityIcons name={name as React.ComponentProps<typeof MaterialCommunityIcons>['name']} color={color} size={size}/>;}
export function Button({label,onPress,secondary=false,disabled=false,icon,style}:{label:string;onPress:()=>void;secondary?:boolean;disabled?:boolean;icon?:string;style?:StyleProp<ViewStyle>}) {
 return <Pressable accessibilityRole="button" accessibilityLabel={label} accessibilityState={{disabled}} disabled={disabled} onPress={onPress} style={({pressed})=>[s.button,secondary&&s.secondary,disabled&&{opacity:.5},pressed&&{opacity:.82,transform:[{scale:.985}]},style]}>{icon&&<Icon name={icon} color={secondary?colors.navy:'#fff'} size={18}/>}<T style={[s.buttonText,secondary&&{color:colors.navy}]}>{label}</T></Pressable>;
}
export function IconButton({name,label,onPress}:{name:string;label:string;onPress:()=>void}) {return <Pressable accessibilityRole="button" accessibilityLabel={label} onPress={onPress} style={({pressed})=>[s.iconButton,pressed&&{opacity:.72,transform:[{scale:.94}]}]}><Icon name={name}/></Pressable>;}
export function Field({label:caption,...props}:TextInputProps&{label:string}){return <View style={{gap:6}}><T style={s.label}>{caption}</T><TextInput accessibilityLabel={caption} placeholderTextColor={colors.muted} {...props} style={[s.input,props.multiline&&{minHeight:100,textAlignVertical:'top'},props.style]}/></View>;}
export function Chips({items,selected,onSelect}:{items:string[];selected:string|string[];onSelect:(value:string)=>void}) {return <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={s.chips} keyboardShouldPersistTaps="handled">{items.map(item=>{const active=Array.isArray(selected)?selected.includes(item):selected===item;return <Pressable key={item} accessibilityRole="button" accessibilityState={{selected:active}} onPress={()=>onSelect(item)} style={[s.chip,active&&s.chipOn]}><T style={[s.chipText,active&&{color:'#fff'}]}>{item}</T></Pressable>;})}</ScrollView>;}
export function Reveal({children,delay=0,style}:{children:React.ReactNode;delay?:number;style?:StyleProp<ViewStyle>}){const opacity=useRef(new Animated.Value(0)).current,translate=useRef(new Animated.Value(14)).current;useEffect(()=>{let live=true;void AccessibilityInfo.isReduceMotionEnabled().then(reduced=>{if(!live)return;if(reduced){opacity.setValue(1);translate.setValue(0);return;}Animated.parallel([Animated.timing(opacity,{toValue:1,duration:280,delay,easing:Easing.out(Easing.cubic),useNativeDriver:true}),Animated.timing(translate,{toValue:0,duration:320,delay,easing:Easing.out(Easing.cubic),useNativeDriver:true})]).start();});return()=>{live=false;};},[delay,opacity,translate]);return <Animated.View style={[style,{opacity,transform:[{translateY:translate}]}]}>{children}</Animated.View>;}
export function Screen({children}:{children:React.ReactNode}) {return <Reveal style={{flex:1}}><ScrollView style={s.screen} keyboardShouldPersistTaps="handled" contentContainerStyle={s.scroll}>{children}</ScrollView></Reveal>;}
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
 const uri=assetUrl(item.imageUrl||item.bannerImage||item.bannerUrl||item.coverImage||item.logoUrl,origin),meta=text(item.category||item.country||item.badge||item.code),description=text(item.tagline||item.shortDesc||item.subtitle||item.excerpt||item.comment||item.summary||item.description),detail=[item.date&&text(item.date),item.time&&text(item.time),item.duration&&text(item.duration),item.location&&text(item.location)].filter(Boolean).join(' · ');
 return <View style={[s.itemCard,compact&&{width:260}]}><Pressable accessibilityRole="button" accessibilityLabel={'View '+title(item)} onPress={onPress}>{uri?<ImageBackground source={{uri}} style={[s.itemHero,compact&&{height:118}]} imageStyle={s.itemHeroImage}><View style={s.itemScrim}/>{meta!=='Not provided'?<View style={s.itemBadge}><T style={s.itemBadgeText}>{meta}</T></View>:null}<T numberOfLines={2} style={s.itemHeroTitle}>{title(item)}</T></ImageBackground>:<View style={[s.itemHero,s.itemFallback]}><Icon name="image-outline" color="#94a3b8" size={30}/><T numberOfLines={2} style={[s.itemHeroTitle,{position:'absolute',left:12,right:12,bottom:12}]}>{title(item)}</T></View>}<View style={s.itemBody}><T numberOfLines={3} style={s.itemDescription}>{description}</T>{detail?<View style={s.itemMeta}><Icon name="information-outline" color="#64748b" size={14}/><T numberOfLines={2} style={s.itemMetaText}>{detail}</T></View>:null}<View style={s.itemFooter}><T style={s.itemLink}>View details</T><View style={s.itemAction}><Icon name="open-in-new" color="#be123c" size={15}/></View></View></View></Pressable>{onSave?<Pressable accessibilityRole="button" accessibilityLabel={saved?'Remove saved item':'Save item'} accessibilityState={{selected:saved}} onPress={onSave} style={({pressed})=>[s.itemSave,pressed&&{opacity:.7,transform:[{scale:.94}]}]}><Icon name={saved?'bookmark':'bookmark-outline'} color={saved?colors.red:'#fff'} size={19}/></Pressable>:null}</View>;
}
export async function openExternal(url:string){try{if(!isNativeAppLink(url))throw new Error('Unsupported link');await Linking.openURL(url);}catch{Alert.alert('Could not open link','The required native app is unavailable. No browser page was opened.');}}
export const s=StyleSheet.create({
 text:{fontFamily:'Jakarta',fontSize:14,lineHeight:21,color:colors.navy},
 screen:{flex:1,backgroundColor:colors.canvas},
 scroll:{padding:14,gap:16,paddingBottom:112},
 heading:{fontFamily:'JakartaExtra',fontSize:23,lineHeight:30},
 title:{fontFamily:'JakartaBold',fontSize:16,lineHeight:23},
 itemCard:{position:'relative',borderRadius:21,backgroundColor:'#fff',borderWidth:1,borderColor:'#e2e8f0',overflow:'hidden',shadowColor:'#0f172a',shadowOpacity:.045,shadowRadius:7,shadowOffset:{width:0,height:3},elevation:1},itemHero:{height:142,backgroundColor:'#0f172a',justifyContent:'space-between',padding:12},itemHeroImage:{borderTopLeftRadius:20,borderTopRightRadius:20},itemScrim:{position:'absolute',top:0,right:0,bottom:0,left:0,backgroundColor:'rgba(2,6,23,.45)'},itemFallback:{alignItems:'center',justifyContent:'center',backgroundColor:'#334155'},itemBadge:{alignSelf:'flex-start',borderRadius:9,backgroundColor:'rgba(15,23,42,.82)',borderWidth:1,borderColor:'rgba(255,255,255,.22)',paddingHorizontal:8,paddingVertical:4},itemBadgeText:{fontFamily:'JakartaBold',fontSize:8,color:'#fff'},itemHeroTitle:{fontFamily:'JakartaExtra',fontSize:15,lineHeight:20,color:'#fff'},itemBody:{padding:13,gap:9},itemDescription:{fontSize:10.5,lineHeight:17,color:'#475569'},itemMeta:{borderRadius:12,backgroundColor:'#f8fafc',padding:9,flexDirection:'row',alignItems:'center',gap:7},itemMetaText:{flex:1,fontFamily:'JakartaBold',fontSize:9,lineHeight:13,color:'#64748b'},itemFooter:{borderTopWidth:1,borderTopColor:'#f1f5f9',paddingTop:9,flexDirection:'row',alignItems:'center',justifyContent:'space-between'},itemLink:{fontFamily:'JakartaBold',fontSize:10,color:'#be123c'},itemAction:{width:32,height:32,borderRadius:10,backgroundColor:'#fff1f2',alignItems:'center',justifyContent:'center'},itemSave:{position:'absolute',top:10,right:10,width:40,height:40,borderRadius:14,backgroundColor:'rgba(15,23,42,.78)',borderWidth:1,borderColor:'rgba(255,255,255,.25)',alignItems:'center',justifyContent:'center'},

 label:{fontFamily:'JakartaBold',fontSize:12,lineHeight:18},
 eyebrow:{fontFamily:'JakartaBold',fontSize:11,letterSpacing:1,color:colors.red,textTransform:'uppercase'},
 muted:{color:colors.muted,fontSize:13,lineHeight:20},
 card:{backgroundColor:'#fff',padding:14,borderWidth:1,borderColor:colors.line,borderRadius:20,gap:12,shadowColor:'#0f172a',shadowOpacity:.035,shadowRadius:8,shadowOffset:{width:0,height:3},elevation:1},
 button:{minHeight:48,paddingVertical:12,paddingHorizontal:16,backgroundColor:colors.red,borderRadius:14,alignItems:'center',justifyContent:'center',flexDirection:'row',gap:8},
 secondary:{backgroundColor:'#f1f5f9',borderWidth:1,borderColor:colors.line},
 buttonText:{fontFamily:'JakartaBold',color:'#fff',fontSize:13,textAlign:'center'},
 iconButton:{width:48,height:48,borderRadius:14,backgroundColor:'#f1f5f9',alignItems:'center',justifyContent:'center'},
 input:{fontFamily:'Jakarta',fontSize:16,minHeight:48,borderWidth:1,borderColor:'#cbd5e1',borderRadius:14,padding:12,color:colors.navy,backgroundColor:'#fff'},
 chips:{gap:8,paddingVertical:4},chip:{minHeight:44,paddingHorizontal:14,paddingVertical:10,borderWidth:1,borderColor:colors.line,borderRadius:12,justifyContent:'center',backgroundColor:'#fff'},
 chipOn:{backgroundColor:colors.navy,borderColor:colors.navy},chipText:{fontSize:12,fontFamily:'JakartaBold'},
 row:{flexDirection:'row',gap:10,alignItems:'center'},check:{flexDirection:'row',gap:12,minHeight:48,alignItems:'center',paddingVertical:8},
 error:{padding:14,borderRadius:12,backgroundColor:'#fef2f2',borderWidth:1,borderColor:'#fecaca'},
 hero:{padding:18,minHeight:260,justifyContent:'flex-end',gap:10,backgroundColor:colors.navy,borderRadius:18,overflow:'hidden'},
 heroTitle:{fontFamily:'JakartaExtra',fontSize:29,lineHeight:36,color:'#fff'},
 grid:{flexDirection:'row',flexWrap:'wrap',gap:12},half:{width:'48%',flexGrow:1},
});

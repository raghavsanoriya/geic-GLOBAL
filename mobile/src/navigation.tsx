import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { DefaultTheme, NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import React from 'react';
import { Platform } from 'react-native';
import { Icon } from './components';
import { AdvisorScreen, EvaluateScreen, ExploreScreen, HomeScreen, MoreScreen } from './screens-main';
import { BookingScreen, DestinationDetailScreen, EventDetailScreen, NotificationsScreen, ServiceDetailScreen } from './screens-detail';
import { colors, fonts } from './theme';
import type { RootStackParamList, RootTabParamList } from './types';

const Tabs = createBottomTabNavigator<RootTabParamList>();
const Stack = createNativeStackNavigator<RootStackParamList>();

const tabIcons: Record<keyof RootTabParamList, string> = {
  Home: 'home-variant-outline',
  Explore: 'compass-outline',
  Advisor: 'robot-outline',
  Evaluate: 'clipboard-check-outline',
  More: 'view-grid-outline',
};

function MainTabs() {
  return (
    <Tabs.Navigator
      initialRouteName="Home"
      screenOptions={({ route }) => ({
        headerShown: false,
        freezeOnBlur: true,
        tabBarActiveTintColor: colors.red,
        tabBarInactiveTintColor: colors.muted,
        tabBarLabel: route.name === 'Advisor' ? 'AI Advisor' : route.name,
        tabBarLabelStyle: { fontFamily: fonts.bold, fontSize: 10, marginTop: 2 },
        tabBarIcon: ({ color, size }) => <Icon name={tabIcons[route.name]} color={color} size={Math.min(size, 24)} />,
        tabBarStyle: {
          minHeight: Platform.OS === 'ios' ? 82 : 70,
          paddingTop: 7,
          paddingBottom: Platform.OS === 'ios' ? 18 : 8,
          backgroundColor: colors.white,
          borderTopColor: colors.line,
          borderTopWidth: 1,
        },
        tabBarItemStyle: { minHeight: 54 },
      })}
    >
      <Tabs.Screen name="Home" component={HomeScreen} options={{ tabBarAccessibilityLabel: 'Home tab' }} />
      <Tabs.Screen name="Explore" component={ExploreScreen} options={{ tabBarAccessibilityLabel: 'Explore tab' }} />
      <Tabs.Screen name="Advisor" component={AdvisorScreen} options={{ tabBarAccessibilityLabel: 'AI Advisor tab' }} />
      <Tabs.Screen name="Evaluate" component={EvaluateScreen} options={{ tabBarAccessibilityLabel: 'Evaluate tab' }} />
      <Tabs.Screen name="More" component={MoreScreen} options={{ tabBarAccessibilityLabel: 'More tab' }} />
    </Tabs.Navigator>
  );
}

const navigationTheme = {
  ...DefaultTheme,
  colors: { ...DefaultTheme.colors, primary: colors.red, background: colors.canvas, card: colors.white, text: colors.ink, border: colors.line, notification: colors.red },
};

export function AppNavigation() {
  return (
    <NavigationContainer
      theme={navigationTheme}
      linking={{
        prefixes: ['geic://', 'https://geic.in/app'],
        config: {
          screens: {
            MainTabs: '',
            Destination: 'destinations/:id',
            Service: 'services/:id',
            Event: 'events/:id',
            Booking: 'book',
            Notifications: 'notifications',
          },
        },
      }}
    >
      <Stack.Navigator screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.canvas }, animation: 'slide_from_right' }}>
        <Stack.Screen name="MainTabs" component={MainTabs} />
        <Stack.Screen name="Destination" component={DestinationDetailScreen} />
        <Stack.Screen name="Service" component={ServiceDetailScreen} />
        <Stack.Screen name="Event" component={EventDetailScreen} />
        <Stack.Screen name="Booking" component={BookingScreen} options={{ presentation: 'modal', animation: 'slide_from_bottom' }} />
        <Stack.Screen name="Notifications" component={NotificationsScreen} options={{ presentation: 'modal', animation: 'slide_from_bottom' }} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}

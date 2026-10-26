export const colors = {
  red: '#D71920',
  redDark: '#AF1117',
  redSoft: '#FFF0F1',
  navy: '#16233C',
  navyDeep: '#0C172C',
  ink: '#172033',
  muted: '#5E6677',
  canvas: '#F5F7FB',
  white: '#FFFFFF',
  line: '#DDE2EA',
  softBlue: '#EAF0FA',
  softGold: '#FFF5E4',
  success: '#117A55',
  danger: '#B42318',
} as const;

export const fonts = {
  regular: 'PlusJakartaSans_400Regular',
  medium: 'PlusJakartaSans_500Medium',
  semiBold: 'PlusJakartaSans_600SemiBold',
  bold: 'PlusJakartaSans_700Bold',
  extraBold: 'PlusJakartaSans_800ExtraBold',
} as const;

export const spacing = { xs: 4, sm: 8, md: 16, lg: 24, xl: 32, xxl: 48 } as const;

export const shadows = {
  card: {
    shadowColor: colors.navyDeep,
    shadowOpacity: 0.08,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 5 },
    elevation: 2,
  },
};

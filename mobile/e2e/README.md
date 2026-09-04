# Expo Go mobile verification

The Maestro flow in `maestro/expo-go-smoke.yaml` applies AUITestAgent's
interaction-then-verification pattern to the Expo Go app: every action is
followed by a visible UI oracle. It checks the hero CTA feedback, destination
search, service action and counselling form without calling a real provider or
mutating data.

Run it against a development session:

```sh
npx expo start --go
maestro test e2e/maestro/expo-go-smoke.yaml
```

`host.exp.exponent` is Expo Go's Android package id. For a custom development
build, override `appId` with that build's application id.

For a fast local web smoke run across the supported phone widths, start Expo
on port 8083 and run:

```sh
npm run test:ui:local
```

This Playwright flow covers the five-tab shell, quick-action details, search
empty state, event registration feedback, service routing, form validation and
the successful counselling state without calling a backend.

# Expo Go mobile verification

The Maestro flow in `maestro/expo-go-smoke.yaml` applies AUITestAgent's
interaction-then-verification pattern to the Expo Go app: every action is
followed by a visible UI oracle. It checks the hero CTA feedback, smart-tool
search and navigation tabs without calling a real provider or mutating data.

Run it against a development session:

```sh
npx expo start --go
maestro test e2e/maestro/expo-go-smoke.yaml
```

`host.exp.exponent` is Expo Go's Android package id. For a custom development
build, override `appId` with that build's application id.

import {StrictMode} from 'react';
import {createRoot} from 'react-dom/client';
import App from './App.tsx';
import './index.css';
import { BookmarksProvider } from './context/BookmarksContext';
if (new URLSearchParams(window.location.search).has('native')) {
  document.documentElement.dataset.native = '1';
}


createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BookmarksProvider>
      <App />
    </BookmarksProvider>
  </StrictMode>,
);

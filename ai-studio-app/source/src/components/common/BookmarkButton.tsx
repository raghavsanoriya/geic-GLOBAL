import React, { useState } from 'react';
import { Heart } from 'lucide-react';
import { useBookmarks, BookmarkType } from '../../context/BookmarksContext';

interface BookmarkButtonProps {
  type: BookmarkType;
  id: string;
  title?: string;
  size?: 'xs' | 'sm' | 'md' | 'lg';
  variant?: 'glass' | 'white' | 'dark' | 'outline' | 'ghost' | 'pill';
  showText?: boolean;
  className?: string;
}

export const BookmarkButton: React.FC<BookmarkButtonProps> = ({
  type,
  id,
  title,
  size = 'sm',
  variant = 'glass',
  showText = false,
  className = '',
}) => {
  const { isSaved, toggleSave } = useBookmarks();
  const saved = isSaved(type, id);
  const [justToggled, setJustToggled] = useState(false);

  const handleClick = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    toggleSave(type, id, title);
    setJustToggled(true);
    setTimeout(() => setJustToggled(false), 300);
  };

  const sizeClasses = {
    xs: 'w-6 h-6 p-1 text-xs',
    sm: 'w-7 h-7 p-1.5 text-xs',
    md: 'w-8 h-8 p-1.5 text-sm',
    lg: 'w-9 h-9 p-2 text-base',
  }[size];

  const iconSizes = {
    xs: 'w-3 h-3',
    sm: 'w-3.5 h-3.5',
    md: 'w-4 h-4',
    lg: 'w-4.5 h-4.5',
  }[size];

  let variantClasses = '';
  switch (variant) {
    case 'glass':
      variantClasses = saved
        ? 'bg-rose-500/90 text-white shadow-sm border border-rose-400/40 backdrop-blur-md'
        : 'bg-slate-900/60 hover:bg-slate-900/80 text-white/90 hover:text-white border border-white/20 backdrop-blur-md';
      break;
    case 'white':
      variantClasses = saved
        ? 'bg-rose-50 text-rose-600 border border-rose-200 shadow-2xs'
        : 'bg-white hover:bg-slate-50 text-slate-400 hover:text-rose-500 border border-slate-200 shadow-2xs';
      break;
    case 'dark':
      variantClasses = saved
        ? 'bg-rose-600 text-white border border-rose-500 shadow-xs'
        : 'bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-rose-400 border border-slate-700';
      break;
    case 'outline':
      variantClasses = saved
        ? 'bg-rose-50 text-rose-600 border border-rose-300'
        : 'bg-transparent hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200';
      break;
    case 'pill':
      return (
        <button
          type="button"
          onClick={handleClick}
          title={saved ? 'Remove from My Saved' : 'Save to My Saved'}
          aria-label={saved ? 'Remove from My Saved' : 'Save to My Saved'}
          className={`px-2.5 py-1 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200 cursor-pointer border ${
            saved
              ? 'bg-rose-50 text-rose-700 border-rose-200 shadow-2xs'
              : 'bg-white hover:bg-slate-50 text-slate-600 hover:text-rose-600 border-slate-200 shadow-2xs'
          } ${justToggled ? 'scale-105' : ''} ${className}`}
        >
          <Heart
            className={`w-3.5 h-3.5 transition-transform duration-200 ${
              saved ? 'fill-rose-500 text-rose-500 scale-110' : 'text-slate-400'
            }`}
          />
          <span>{saved ? 'Saved' : 'Save'}</span>
        </button>
      );
    case 'ghost':
    default:
      variantClasses = saved
        ? 'text-rose-600 hover:bg-rose-50'
        : 'text-slate-400 hover:text-rose-600 hover:bg-slate-100';
      break;
  }

  return (
    <button
      type="button"
      onClick={handleClick}
      title={saved ? 'Saved in My Collection (Click to remove)' : 'Save to My Collection'}
      aria-label={saved ? 'Saved in My Collection (Click to remove)' : 'Save to My Collection'}
      className={`rounded-full flex items-center justify-center transition-all duration-200 cursor-pointer shrink-0 ${sizeClasses} ${variantClasses} ${
        justToggled ? 'scale-125' : 'hover:scale-110'
      } ${className}`}
    >
      <Heart
        className={`${iconSizes} transition-all duration-200 ${
          saved ? 'fill-rose-500 text-rose-500' : ''
        }`}
      />
      {showText && (
        <span className="ml-1 text-[11px] font-bold">
          {saved ? 'Saved' : 'Save'}
        </span>
      )}
    </button>
  );
};

import React from 'react';
import { X, Calendar, Clock, BookOpen, Share2, CheckCircle2, ArrowRight } from 'lucide-react';
import { BlogPost } from '../../types';

interface BlogDetailModalProps {
  post: BlogPost | null;
  onClose: () => void;
  onOpenBooking: () => void;
}

export const BlogDetailModal: React.FC<BlogDetailModalProps> = ({
  post,
  onClose,
  onOpenBooking,
}) => {
  if (!post) return null;

  const handleShare = () => {
    if (navigator.share) {
      navigator.share({
        title: post.title,
        text: post.excerpt,
        url: window.location.href,
      }).catch(() => {});
    } else {
      navigator.clipboard.writeText(window.location.href);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/70 backdrop-blur-sm animate-fadeIn">
      <div 
        className="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border border-slate-100 animate-slideUp"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Sticky Modal Header */}
        <div className="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-white/95 backdrop-blur-sm sticky top-0 z-10">
          <div className="flex items-center space-x-2">
            <span className="text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
              {post.category}
            </span>
            <span className="text-xs text-slate-400 font-medium flex items-center gap-1">
              <Clock className="w-3 h-3" />
              {post.readTime}
            </span>
          </div>

          <div className="flex items-center space-x-2">
            <button
              onClick={handleShare}
              className="p-1.5 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer"
              title="Share Article"
            >
              <Share2 className="w-4 h-4" />
            </button>
            <button
              onClick={onClose}
              className="p-1.5 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer"
              aria-label="Close"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Scrollable Content */}
        <div className="overflow-y-auto px-5 py-4 space-y-5">
          {/* Cover Image */}
          <div className="relative h-48 sm:h-64 rounded-2xl overflow-hidden shadow-sm">
            <img
              src={post.coverImage}
              alt={post.title}
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent" />
            <div className="absolute bottom-3 left-3 right-3 text-white">
              <div className="flex flex-wrap gap-1.5">
                {post.tags.map((tag, idx) => (
                  <span
                    key={idx}
                    className="text-[9px] font-bold bg-white/20 backdrop-blur-md px-2 py-0.5 rounded-md border border-white/20"
                  >
                    #{tag}
                  </span>
                ))}
              </div>
            </div>
          </div>

          {/* Title & Author Meta */}
          <div className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
              {post.title}
            </h2>

            <div className="flex items-center justify-between pt-1 border-b border-slate-100 pb-3">
              <div className="flex items-center space-x-3">
                {post.author.avatar ? (<img
                  src={post.author.avatar}
                  alt={post.author.name}
                  className="w-9 h-9 rounded-full object-cover border border-slate-200"
                />) : <span aria-hidden="true" className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{post.author.name.slice(0, 1)}</span>}
                <div>
                  <h4 className="text-xs font-bold text-slate-900 leading-none">
                    {post.author.name}
                  </h4>
                  <p className="text-[10px] text-slate-500 mt-0.5">{post.author.role}</p>
                </div>
              </div>

              <div className="text-right text-[11px] text-slate-400 font-medium flex items-center gap-1">
                <Calendar className="w-3.5 h-3.5 text-slate-400" />
                <span>{post.publishedDate}</span>
              </div>
            </div>
          </div>

          {/* Excerpt Lead Paragraph */}
          <div className="p-3.5 rounded-2xl bg-red-50/60 border border-red-100 text-xs text-slate-700 font-medium leading-relaxed italic">
            "{post.excerpt}"
          </div>

          {/* Key Takeaways Box */}
          {post.keyTakeaways && post.keyTakeaways.length > 0 && (
            <div className="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
              <div className="flex items-center space-x-2 text-xs font-black text-amber-900 uppercase tracking-wider">
                <BookOpen className="w-4 h-4 text-amber-600" />
                <span>Key Takeaways & Action Points</span>
              </div>
              <ul className="space-y-1.5 text-xs text-slate-700">
                {post.keyTakeaways.map((point, idx) => (
                  <li key={idx} className="flex items-start space-x-2">
                    <CheckCircle2 className="w-3.5 h-3.5 text-amber-600 mt-0.5 shrink-0" />
                    <span>{point}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {/* Article Body Paragraphs */}
          <div className="space-y-3.5 text-xs text-slate-700 leading-relaxed font-normal">
            {post.content.map((paragraph, idx) => (
              <p key={idx} className="text-justify">
                {paragraph}
              </p>
            ))}
          </div>

          {/* In-Article CTA Banner */}
          <div className="p-4 rounded-2xl bg-gradient-to-r from-red-600 to-rose-700 text-white space-y-2 shadow-md">
            <h4 className="font-extrabold text-sm">Need Personalized Guidance on this Topic?</h4>
            <p className="text-[11px] text-red-100 leading-snug">
              Trans Globe Indore provides 100% free personalized one-on-one strategy sessions for profile evaluation, scholarships, and visa processing.
            </p>
            <button
              onClick={() => {
                onClose();
                onOpenBooking();
              }}
              className="mt-1 w-full bg-white text-red-600 hover:bg-red-50 font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition cursor-pointer shadow-xs"
            >
              <span>Book Free Strategy Session at Indore Office</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

        {/* Modal Footer */}
        <div className="p-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-xs">
          <span className="text-[11px] text-slate-500">
            Trans Globe Indore • Study Abroad Knowledge Base
          </span>
          <button
            onClick={onClose}
            className="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 font-bold text-slate-800 transition cursor-pointer text-xs"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};

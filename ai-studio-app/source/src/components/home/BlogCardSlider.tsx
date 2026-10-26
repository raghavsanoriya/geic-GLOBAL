import React from 'react';
import { Newspaper, Clock, Calendar, ArrowRight } from 'lucide-react';
import { GEIC_BLOGS } from '../../data/liveCatalog';
import { BlogPost } from '../../types';

interface BlogCardSliderProps {
  onSelectBlog: (post: BlogPost) => void;
}

export const BlogCardSlider: React.FC<BlogCardSliderProps> = ({ onSelectBlog }) => {
  return (
    <div className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
      {/* App Section Header with Nav Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2.5">
          <div className="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 shadow-xs">
            <Newspaper className="w-4 h-4" />
          </div>
          <div>
            <div className="flex items-center space-x-2">
              <h3 className="font-black text-sm text-slate-900 tracking-tight">
                Study Abroad Guides & News
              </h3>
              <span className="text-[10px] font-bold bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full border border-purple-200">
                Published guides
              </span>
            </div>
            <p className="text-[11px] text-slate-500 font-medium">
              Visa updates, scholarship strategies, and test-taking masterclasses
            </p>
          </div>
        </div>

      </div>

      {/* Horizontal Slider Track */}
      <div
        tabIndex={0}
        aria-label="Swipe to browse articles"
        className="flex overflow-x-auto gap-3.5 pb-2 pt-1 scrollbar-none snap-x snap-mandatory scroll-smooth -mx-1 px-1"
      >
        {GEIC_BLOGS.map((post: BlogPost) => (
          <div
            key={post.id}
            onClick={() => onSelectBlog(post)}
            className="min-w-[250px] max-w-[250px] bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md hover:border-purple-300 transition-all duration-200 cursor-pointer shrink-0 snap-start flex flex-col justify-between group select-none"
          >
            {/* Blog Cover Image */}
            <div className="relative h-28 w-full bg-slate-900 overflow-hidden">
              <img
                src={post.coverImage}
                alt={post.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />
              
              <div className="absolute top-2.5 left-2.5 bg-purple-600 text-white font-black text-[9px] px-2 py-0.5 rounded-md shadow-xs">
                {post.category}
              </div>

              <div className="absolute top-2.5 right-2.5 bg-slate-900/80 backdrop-blur-xs text-slate-200 font-bold text-[9px] px-1.5 py-0.5 rounded border border-white/20 flex items-center gap-1">
                <Clock className="w-2.5 h-2.5" />
                <span>{post.readTime}</span>
              </div>

              <div className="absolute bottom-2 left-2.5 right-2.5 text-white">
                <h4 className="font-extrabold text-xs leading-snug line-clamp-2 group-hover:text-purple-300 transition-colors">
                  {post.title}
                </h4>
              </div>
            </div>

            {/* Content Preview & Author */}
            <div className="p-3 space-y-2 text-xs flex-1 flex flex-col justify-between">
              <div>
                <p className="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                  {post.excerpt}
                </p>

                <div className="mt-2 flex items-center space-x-2 pt-2 border-t border-slate-200/70">
                  {post.author.avatar ? (<img
                    src={post.author.avatar}
                    alt={post.author.name}
                    className="w-5 h-5 rounded-full object-cover border border-slate-200 shrink-0"
                  />) : <span aria-hidden="true" className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{post.author.name.slice(0, 1)}</span>}
                  <div className="truncate">
                    <span className="text-[10px] font-bold text-slate-800 block truncate">
                      {post.author.name}
                    </span>
                  </div>
                </div>
              </div>

              {/* Action Button */}
              <div className="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] font-bold text-purple-700 group-hover:text-purple-800">
                <span>Read Full Article</span>
                <span className="w-5 h-5 rounded-full bg-purple-100 group-hover:bg-purple-600 group-hover:text-white flex items-center justify-center transition-colors">
                  <ArrowRight className="w-3 h-3" />
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Section Footer */}
      <div className="pt-1 flex items-center justify-between text-xs border-t border-slate-100">
        <span className="text-[11px] text-slate-500 font-medium">
          From the GEIC published blog
        </span>
        <span className="text-xs font-bold text-purple-600 flex items-center gap-1">
          <span>Click any card to read</span>
        </span>
      </div>
    </div>
  );
};

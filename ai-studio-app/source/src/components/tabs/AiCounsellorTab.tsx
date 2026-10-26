import React, { useState, useRef, useEffect } from 'react';
import { 
  Send, 
  Sparkles, 
  Bot, 
  User, 
  RotateCcw, 
  PhoneCall, 
  Calendar, 
  CheckCircle2, 
  MessageSquare,
  ArrowRight
} from 'lucide-react';
import { GEIC_BRAND } from '../../data/liveCatalog';
import { ChatMessage } from '../../types';

interface AiCounsellorTabProps {
  onOpenBooking: () => void;
}

export const AiCounsellorTab: React.FC<AiCounsellorTabProps> = ({ onOpenBooking }) => {
  const [messages, setMessages] = useState<ChatMessage[]>([
    {
      id: 'init-1',
      sender: 'assistant',
      text: `Hello! I am the GEIC study assistant. Ask me about the study destinations, services and preparation options in our catalogue.`,
      timestamp: Date.now(),
      suggestedActions: [
        '🇩🇪 Is studying in Germany free?',
        '🇦🇺 Australia Go8 & Post-Study Work',
        '🇬🇧 Explore UK study options',
        '💰 How to get 2026 Scholarships?',
      ],
    },
  ]);

  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages, loading]);

  const handleSendMessage = async (textToSend?: string) => {
    const query = (textToSend || inputText).trim();
    if (!query || loading) return;

    const userMsg: ChatMessage = {
      id: `user_${Date.now()}`,
      sender: 'user',
      text: query,
      timestamp: Date.now(),
    };

    setMessages((prev) => [...prev, userMsg]);
    setInputText('');
    setLoading(true);

    try {
      const response = await fetch('/api/mobile/study-assistant/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message: query,
          history: messages.slice(-6).map((m) => ({
            role: m.sender === 'user' ? 'user' : 'assistant',
            content: m.text,
          })),
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to get advisor response');
      }

      const data = await response.json();

      if (typeof data.reply !== 'string' || !data.reply.trim()) throw new Error('No response received');
      const assistantMsg: ChatMessage = {
        id: `assistant_${Date.now()}`,
        sender: 'assistant',
        text: data.reply,
        timestamp: Date.now(),
        suggestedActions: [
          'Book Free 1-on-1 Indore Session',
          'Explore university options',
          'Check application readiness',
        ],
      };

      setMessages((prev) => [...prev, assistantMsg]);
    } catch (err: any) {
      const errorMsg: ChatMessage = {
        id: `assistant_err_${Date.now()}`,
        sender: 'assistant',
        text: 'The study assistant is unavailable right now. Please try again or book a counselling session.',
        timestamp: Date.now(),
      };
      setMessages((prev) => [...prev, errorMsg]);
    } finally {
      setLoading(false);
    }
  };

  const resetChat = () => {
    setMessages([
      {
        id: `reset_${Date.now()}`,
        sender: 'assistant',
        text: `Chat restarted! Feel free to ask anything about courses, English test bands (IELTS/PTE), fee structures, or 2026 intake deadlines.`,
        timestamp: Date.now(),
        suggestedActions: [
          '🇩🇪 Germany Public Universities',
          '🇦🇺 Australia Subclass 500 Visa',
          '🇨🇦 Canada 3-Yr PGWP Updates',
        ],
      },
    ]);
  };

  return (
    <div className="flex flex-col h-[calc(100vh-140px)] max-w-[440px] mx-auto text-slate-800">
      {/* Advisor Top Bar */}
      <div className="pt-2 px-4 pb-2 bg-white border-b border-slate-100 flex items-center justify-between shrink-0">
        <div className="flex items-center space-x-2.5">
          <div className="relative">
            <div className="w-9 h-9 rounded-2xl bg-gradient-to-tr from-red-600 via-rose-500 to-amber-500 flex items-center justify-center text-white shadow-xs">
              <Bot className="w-5 h-5" />
            </div>
            <span className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full" />
          </div>
          <div>
            <div className="flex items-center space-x-1.5">
              <h3 className="font-black text-xs text-slate-900">Trans Globe Advisor</h3>
              <span className="text-[9px] bg-red-100 text-red-700 font-extrabold px-1.5 py-0.2 rounded-md">
                AI
              </span>
            </div>
            <p className="text-[10px] text-slate-500">Based on GEIC catalogue guidance</p>
          </div>
        </div>

        <div className="flex items-center space-x-1">
          <button
            onClick={resetChat}
            className="p-2 text-slate-400 hover:text-slate-700 transition rounded-xl"
            title="Clear and restart conversation"
          >
            <RotateCcw className="w-4 h-4" />
          </button>

          <button
            onClick={onOpenBooking}
            className="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2.5 py-1.5 rounded-xl text-[11px] transition flex items-center space-x-1 border border-red-200"
          >
            <Calendar className="w-3.5 h-3.5" />
            <span>Book Indore Call</span>
          </button>
        </div>
      </div>

      {/* Messages Scroll Area */}
      <div className="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs">
        {messages.map((msg) => {
          const isUser = msg.sender === 'user';

          return (
            <div
              key={msg.id}
              className={`flex items-start gap-2 ${isUser ? 'flex-row-reverse' : 'flex-row'}`}
            >
              <div
                className={`w-7 h-7 rounded-xl flex items-center justify-center text-xs shrink-0 font-bold ${
                  isUser
                    ? 'bg-slate-900 text-white'
                    : 'bg-red-600 text-white'
                }`}
              >
                {isUser ? <User className="w-3.5 h-3.5" /> : <Bot className="w-3.5 h-3.5" />}
              </div>

              <div className={`space-y-1.5 max-w-[84%]`}>
                <div
                  className={`p-3 rounded-2xl leading-relaxed whitespace-pre-wrap ${
                    isUser
                      ? 'bg-slate-900 text-white rounded-tr-xs font-medium'
                      : 'bg-white border border-slate-200/90 text-slate-800 rounded-tl-xs shadow-xs'
                  }`}
                >
                  {msg.text}
                </div>

                {/* Suggested prompt chips */}
                {msg.suggestedActions && msg.suggestedActions.length > 0 && (
                  <div className="flex flex-wrap gap-1 pt-1">
                    {msg.suggestedActions.map((action, i) => (
                      <button
                        key={i}
                        onClick={() => {
                          if (action.includes('Book')) {
                            onOpenBooking();
                          } else {
                            handleSendMessage(action);
                          }
                        }}
                        className="bg-slate-100 hover:bg-red-50 hover:text-red-700 hover:border-red-200 border border-slate-200 text-slate-700 text-[10px] font-semibold px-2 py-1 rounded-lg transition text-left cursor-pointer"
                      >
                        {action}
                      </button>
                    ))}
                  </div>
                )}
              </div>
            </div>
          );
        })}

        {loading && (
          <div className="flex items-start gap-2">
            <div className="w-7 h-7 rounded-xl bg-red-600 text-white flex items-center justify-center text-xs shrink-0">
              <Bot className="w-3.5 h-3.5" />
            </div>
            <div className="p-3 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center space-x-1.5">
              <div className="w-2 h-2 rounded-full bg-red-600 animate-bounce" />
              <div className="w-2 h-2 rounded-full bg-red-400 animate-bounce [animation-delay:0.2s]" />
              <div className="w-2 h-2 rounded-full bg-amber-400 animate-bounce [animation-delay:0.4s]" />
              <span className="text-[11px] text-slate-500 font-medium ml-1">
                Consulting Trans Globe knowledge base...
              </span>
            </div>
          </div>
        )}

        <div ref={messagesEndRef} />
      </div>

      {/* Chat Input Dock */}
      <div className="p-3 bg-white border-t border-slate-100 shrink-0">
        <form
          onSubmit={(e) => {
            e.preventDefault();
            handleSendMessage();
          }}
          className="flex items-center space-x-2"
        >
          <input
            type="text"
            value={inputText}
            onChange={(e) => setInputText(e.target.value)}
            placeholder="Ask about courses, Germany fees, IELTS..."
            className="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500 transition"
          />
          <button
            type="submit"
            disabled={!inputText.trim() || loading}
            className="bg-red-600 hover:bg-red-700 disabled:opacity-40 text-white p-2.5 rounded-2xl transition shadow-sm cursor-pointer shrink-0"
          >
            <Send className="w-4 h-4" />
          </button>
        </form>
        <p className="text-[9px] text-center text-slate-400 mt-1.5">
          Official guidance from Trans Globe Indore • Free counselling: +91 98266 66886
        </p>
      </div>
    </div>
  );
};

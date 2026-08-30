import React, { useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { Button, Cell, ConfigProvider, Empty, Search, Tag } from 'react-vant';
import 'react-vant/lib/index.css';
import '../css/ai-agents.css';

const mount = document.getElementById('ai-agents-vant');
const catalogNode = document.getElementById('ai-agents-catalog');

if (mount && catalogNode) {
    let agents = [];

    try {
        agents = JSON.parse(catalogNode.textContent || '[]');
    } catch {
        agents = [];
    }

    const app = createRoot(mount);
    app.render(
        <ConfigProvider themeVars={{ primaryColor: '#e31e24', buttonPrimaryBackground: '#e31e24' }}>
            <AgentQuickPicker agents={agents} />
        </ConfigProvider>,
    );
}

function AgentQuickPicker({ agents }) {
    const [query, setQuery] = useState('');
    const [category, setCategory] = useState('All');
    const [selectedSlug, setSelectedSlug] = useState(agents[0]?.slug || '');
    const categories = useMemo(
        () => ['All', ...new Set(agents.map((agent) => agent.category).filter(Boolean))],
        [agents],
    );
    const filtered = useMemo(() => {
        const normalized = query.trim().toLowerCase();

        return agents.filter((agent) => {
            const matchesCategory = category === 'All' || agent.category === category;
            const haystack = [agent.title, agent.description, agent.framework, ...(agent.tags || [])]
                .join(' ')
                .toLowerCase();

            return matchesCategory && (!normalized || haystack.includes(normalized));
        });
    }, [agents, category, query]);
    const selected = agents.find((agent) => agent.slug === selectedSlug) || filtered[0];

    const chooseAgent = (agent) => {
        setSelectedSlug(agent.slug);
        const select = document.querySelector('[data-agent-form] select[name="agent"]');

        if (select) {
            select.value = agent.slug;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    const focusWorkspace = () => {
        if (selected) {
            chooseAgent(selected);
        }

        document.querySelector('[data-agent-form] textarea[name="message"]')?.focus();
    };

    return (
        <section className="tg-vant-panel" aria-labelledby="tg-vant-title">
            <div className="tg-vant-panel__header">
                <div>
                    <span className="tg-vant-panel__eyebrow">Quick pick</span>
                    <h2 id="tg-vant-title">Choose a starting point</h2>
                </div>
                <Tag round type="primary">React Vant</Tag>
            </div>
            <Search
                value={query}
                onChange={setQuery}
                placeholder="Search the agent catalog"
                shape="round"
                clearable
                aria-label="Search the agent catalog"
            />
            <div className="tg-vant-panel__categories" role="tablist" aria-label="Agent categories">
                {categories.map((item) => (
                    <Button
                        key={item}
                        size="small"
                        round
                        hairline
                        type={category === item ? 'primary' : 'default'}
                        role="tab"
                        aria-selected={category === item}
                        onClick={() => setCategory(item)}
                    >
                        {item}
                    </Button>
                ))}
            </div>
            <div className="tg-vant-panel__list">
                {filtered.slice(0, 4).map((agent) => (
                    <Cell
                        key={agent.slug}
                        title={agent.title}
                        label={`${agent.category} · ${agent.framework}`}
                        value={selectedSlug === agent.slug ? 'Selected' : undefined}
                        isLink
                        clickable
                        onClick={() => chooseAgent(agent)}
                        className={selectedSlug === agent.slug ? 'is-selected' : ''}
                    />
                ))}
                {!filtered.length && <Empty image="default" description="No matching agents" />}
            </div>
            <div className="tg-vant-panel__footer">
                <span>{selected ? `Selected: ${selected.title}` : 'Select an agent to continue'}</span>
                <Button type="primary" size="small" onClick={focusWorkspace} disabled={!selected}>
                    Use this agent
                </Button>
            </div>
        </section>
    );
}

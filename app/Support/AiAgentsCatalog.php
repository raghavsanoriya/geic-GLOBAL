<?php

namespace App\Support;

/**
 * Selected study-abroad-relevant examples imported from the upstream
 * 500-AI-Agents-Projects repository. The public site catalogs them only; it
 * never executes third-party Python code during a web request.
 */
class AiAgentsCatalog
{
    private const REPOSITORY_URL = 'https://github.com/ashishpatel26/500-AI-Agents-Projects';

    public static function all(): array
    {
        $agents = [
            self::agent('01-web-research-agent', 'Web Research Agent', 'Searches the web for a topic and synthesizes a structured research report for destination, university and market shortlists.', 'LangGraph', 'Research', 'Intermediate', ['research', 'web search', 'RAG'], 'gpt-4o-mini'),
            self::agent('03-pdf-qa-agent', 'PDF Q&A Agent', 'Loads a PDF and answers questions about offer letters, handbooks, policies and other study documents with conversation history.', 'LlamaIndex', 'Document analysis', 'Beginner', ['PDF', 'RAG', 'document analysis'], 'gpt-4o-mini'),
            self::agent('05-email-drafting-agent', 'Email Drafting Agent', 'Uses a two-agent workflow to understand context and draft a clear, polished email to a university, lender or student.', 'CrewAI', 'Communication', 'Beginner', ['email', 'communication', 'productivity'], 'gpt-4o-mini'),
            self::agent('08-data-analysis-agent', 'Data Analysis Agent', 'Chats with CSV or Excel data using natural-language questions and pandas-powered analysis for practical reporting.', 'LangChain', 'Analytics', 'Intermediate', ['data analysis', 'pandas', 'CSV'], 'gpt-4o'),
            self::agent('12-travel-planner-agent', 'Travel Planner Agent', 'Creates a personalised itinerary with destination research, day-by-day activities and a budget plan for pre-departure travel.', 'CrewAI', 'Travel planning', 'Intermediate', ['travel', 'itinerary', 'budget'], 'gpt-4o-mini'),
            self::agent('13-customer-support-agent', 'Customer Support Agent', 'Uses retrieval-augmented answers and escalation routing to help teams handle recurring student and customer questions.', 'LangGraph', 'Student support', 'Advanced', ['support', 'knowledge base', 'escalation'], 'gpt-4o-mini'),
        ];
        foreach ([
            ['ai-travel-agent','AI Travel Agent','A travel-planning reference combining destination research, recommendations and itinerary building.','Travel planning',['travel','itinerary','recommendations'],'gold','https://github.com/Shubhamsaboo/awesome-llm-apps/tree/main/starter_ai_agents/ai_travel_agent'],
            ['openai-research-agent','OpenAI Research Agent','A research workflow for gathering sources and synthesizing focused answers from the web.','Research',['research','web search','summarization'],'blue','https://github.com/Shubhamsaboo/awesome-llm-apps/tree/main/starter_ai_agents/openai_research_agent'],
            ['ai-customer-support-agent','AI Customer Support Agent','A customer-support reference for recurring questions and routing conversations to the right next step.','Customer support',['support','knowledge base','routing'],'green','https://github.com/Shubhamsaboo/awesome-llm-apps/tree/main/advanced_ai_agents/single_agent_apps/ai_customer_support_agent'],
            ['chat-with-pdf','Chat with PDF','A document Q&A example for extracting context from uploaded PDFs with retrieval-augmented generation.','PDF / RAG',['PDF','RAG','document Q&A'],'red','https://github.com/Shubhamsaboo/awesome-llm-apps/tree/main/advanced_llm_apps/chat_with_X_tutorials/chat_with_pdf'],
        ] as [$slug,$title,$description,$category,$tags,$accent,$url]) $agents[]=['slug'=>$slug,'title'=>$title,'description'=>$description,'framework'=>'Awesome LLM Apps','industry'=>$category,'category'=>$category,'difficulty'=>'Reference','tags'=>$tags,'language'=>'Python','llm'=>'varies','source_path'=>null,'source_url'=>$url,'url'=>$url,'accent'=>$accent,'readme_url'=>$url,'files'=>[],'install_command'=>null];
        return $agents;
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $agent) {
            if ($agent['slug'] === $slug) {
                return $agent;
            }
        }

        return null;
    }

    public static function repositoryUrl(): string
    {
        return self::REPOSITORY_URL;
    }

    private static function agent(
        string $folder,
        string $title,
        string $description,
        string $framework,
        string $industry,
        string $difficulty,
        array $tags,
        string $llm
    ): array {
        $sourcePath = 'agents/500-ai-agents/'.$folder;

        return [
            'slug' => $folder,
            'title' => $title,
            'description' => $description,
            'framework' => $framework,
            'industry' => $industry,
            'category' => $industry,
            'difficulty' => $difficulty,
            'tags' => $tags,
            'language' => 'Python',
            'llm' => $llm,
            'source_path' => $sourcePath,
            'source_url' => self::REPOSITORY_URL.'/tree/main/agents/'.$folder,
            'url' => self::REPOSITORY_URL.'/tree/main/agents/'.$folder,
            'accent' => match (strtolower($industry)) {
                'document analysis' => 'red',
                'communication' => 'gold',
                'analytics' => 'green',
                'student support' => 'green',
                'travel planning' => 'gold',
                default => 'blue',
            },
            'readme_url' => self::REPOSITORY_URL.'/blob/main/agents/'.$folder.'/README.md',
            'files' => ['agent.py', 'README.md', 'metadata.yaml', 'requirements.txt'],
            'install_command' => 'pip install -r requirements.txt',
        ];
    }
}

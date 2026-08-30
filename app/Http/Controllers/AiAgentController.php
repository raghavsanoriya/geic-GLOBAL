<?php
namespace App\Http\Controllers;
use App\Support\AiAgentsCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class AiAgentController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $data = $request->validate(['agent' => ['required','string','max:100'], 'message' => ['required','string','max:2000']]);
        $agent = AiAgentsCatalog::find($data['agent']);
        if (! $agent) return response()->json(['message' => 'That agent is not in the catalogue.'], 422);
        $key = config('services.study_assistant.api_key');
        if (is_string($key) && trim($key) !== '') {
            try { $res = Http::withToken($key)->acceptJson()->timeout((int) config('services.study_assistant.timeout',20))->post(config('services.study_assistant.endpoint'), ['model' => config('services.study_assistant.model','llama-3.3-70b-versatile'), 'messages' => [['role'=>'system','content'=>"You are the {$agent['title']} for Trans Globe Indore. {$agent['description']} Give concise practical guidance and never invent time-sensitive requirements."], ['role'=>'user','content'=>trim($data['message'])]], 'temperature'=>0.2, 'max_tokens'=>650]); $reply=$res->json('choices.0.message.content'); if($res->successful() && is_string($reply) && trim($reply)!=='') return response()->json(['agent'=>$agent['title'],'reply'=>trim($reply),'source'=>'provider']); } catch (\Throwable) {}
        }
        return response()->json(['agent'=>$agent['title'],'reply'=>'Share more context about your goal, destination, budget or document and I’ll suggest a practical next step. Verify important details with official sources.','source'=>'guided']);
    }
}

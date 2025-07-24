<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Bonus;
use App\Models\BonusHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\BitrixData;
use Maatwebsite\Excel\Facades\Excel;

class BonusController extends Controller
{
    public function index(Request $request): View
    {
        $bonus = Bonus::where('user_id', $request->user()->id)->first();
        $bonus_histories = BonusHistory::where('user_id', $request->user()->id)->get();


        return view('user.bonus', compact('bonus', 'bonus_histories'));
    }

    public function getData(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        $result = $http->get('https://b24-s4obyn.bitrix24.ru/rest/14/vfgi34zbyb0f1zvu/crm.deal.list.json?FILTER[UF_CRM_1623498762]=450&FILTER[UF_CRM_1623498831]=2024-12-16&FILTER[CATEGORY_ID]=8&SELECT[]=OPPORTUNITY&SELECT[]=UF_*&SELECT[]=STAGE_ID&SELECT[]=CONTACT_ID');
        $result = $result->getBody()->getContents();
        $response = json_decode($result, true);
        foreach ($response['result'] as $item) {
            $contact = $http->get('https://b24-s4obyn.bitrix24.ru/rest/14/vfgi34zbyb0f1zvu/crm.contact.get.json?ID='.$item['CONTACT_ID']);
            $contact = $contact->getBody()->getContents();
            $contact = json_decode($contact, true);
            $fullName = $contact['result']['NAME'].' '.$contact['result']['LAST_NAME'].' '.$contact['result']['SECOND_NAME'];
            $factAmount = preg_replace('/\|[A-Z]+$/', '', $item['UF_CRM_1625298929']);
            $ostatokAmount = preg_replace('/\|[A-Z]+$/', '', $item['UF_CRM_1625298951']);

            BitrixData::create([
               'fullName' => $fullName,
               'course_name' => 'Программа профессиональной переподготовки по специальности "Клиническая психология" 16.12.2024-20.04.2026',
               'stage' => $item['STAGE_ID'],
               'amount' => $item['OPPORTUNITY'],
               'fact' => $factAmount,
                'ostatok' => $ostatokAmount,
                'deal_id' => $item['ID'],
            ]);
        }
    }

    public function generateCourse(Request $request){
        $data = [];
        $data = $request->all();

        return Excel::download(new UsersExport($data),'data.xlsx');
    }
}

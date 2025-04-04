<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RDO_Data_Model;

class RDO_Data_Controller extends Controller
{
    public function create(Request $request)
    {
        // Decode the JSON payload into an associative array
        $data = json_decode($request->getContent(), true);

        // Extract sections from the JSON payload
        $hookMain = $data['HookMain'] ?? [];
        $hookControlPanel = $data['HookControlPanel'] ?? [];
        $entry = $data['Entry'] ?? [];

        // Extract name mapping from HookMain->HookName and department mapping from HookMain->HookDepartment
        $hookName = $hookMain['HookName'] ?? [];
        $hookDepartment = $hookMain['HookDepartment'] ?? [];

        // Map the JSON fields to the corresponding table columns
        $rdoData = RDO_Data_Model::create([
            // HookMain -> HookName mapping
            'Name_Lable' => $hookName['Label'] ?? null,
            'Name_ID'    => $hookName['Id'] ?? null,

            // HookMain mapping
            'HookTodaysDate' => $hookMain['HookTodaysDate'] ?? null,
            'HookWhoWillCoverYourShiftIfYouDontHaveYouCanWriteNA' => $hookMain['HookWhoWillCoverYourShiftIfYouDontHaveYouCanWriteNA'] ?? null,
            'HookStartDate' => $hookMain['HookStartDate'] ?? null,
            'HookEndDate'   => $hookMain['HookEndDate'] ?? null,
            'HookDepartment_ID' => $hookDepartment['Id'] ?? null,
            'HookDepartment_Lable' => $hookDepartment['Label'] ?? null,
            'HookHowManyDaysDoYouNeed2' => $hookMain['HookHowManyDaysDoYouNeed2'] ?? null,
            'HookType' => $hookMain['HookType'] ?? null,
            'HookAreYouAbleToProvideAProof' => isset($hookMain['HookAreYouAbleToProvideAProof'])
                ? (is_array($hookMain['HookAreYouAbleToProvideAProof']) ? json_encode($hookMain['HookAreYouAbleToProvideAProof']) : $hookMain['HookAreYouAbleToProvideAProof'])
                : null,
            'HookHowManyDaysDoYouNeed2_IncrementBy' => $hookMain['HookHowManyDaysDoYouNeed2_IncrementBy'] ?? null,
            'HookAreYouAbleToProvideMoreSpecificPlease_IsRequired' => isset($hookMain['HookAreYouAbleToProvideMoreSpecificPlease_IsRequired'])
                ? ($hookMain['HookAreYouAbleToProvideMoreSpecificPlease_IsRequired'] ? 'true' : 'false')
                : null,

            // HookControlPanel mapping
            'DirectManagerName_ID' => $hookControlPanel['DirectManagerName']['Id'] ?? null,
            'DirectManagerName_Lable' => $hookControlPanel['DirectManagerName']['Label'] ?? null,
            'HookApprove' => $hookControlPanel['HookApprove'] ?? null,
            'Note' => $hookControlPanel['Note'] ?? null,

            // Entry mapping
            'AdminLink' => $entry['AdminLink'] ?? null,
            'Status' => $entry['Status'] ?? null,
            'PublicLink' => $entry['PublicLink'] ?? null,
            'InternalLink' => $entry['InternalLink'] ?? null,
            'Entry_Number' => isset($entry['Number']) ? (string)$entry['Number'] : null,
        ]);

        return response()->json([
            'message' => 'RDO data created successfully',
            'data'    => $rdoData
        ], 201);
    }

 
    public function destroy(Request $request)
    {
        // Decode the JSON payload into an associative array
        $data = json_decode($request->getContent(), true);
        $entryNumber = isset($data['Entry']['Number']) ? (string)$data['Entry']['Number'] : null;

        if (!$entryNumber) {
            return response()->json(['message' => 'Entry_Number not provided'], 400);
        }

        // Find the record by Entry_Number
        $rdoData = RDO_Data_Model::where('Entry_Number', $entryNumber)->first();
        if (!$rdoData) {
            return response()->json(['message' => 'RDO data not found'], 404);
        }

        // Delete the record
        $rdoData->delete();

        return response()->json([
            'message' => 'RDO data deleted successfully'
        ], 200);
    }
}

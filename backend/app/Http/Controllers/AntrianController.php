<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use App\Models\Antrian;
use Illuminate\Http\Request;
use App\Events\AntrianDiambil;
use App\Events\AntrianDipanggil;

class AntrianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Antrian::with('loket')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    public function ambilAntrian(Request $request)
    {
        $lastAntrian = Antrian::latest()->first();
        $nextNumber = $lastAntrian ? (int)substr($lastAntrian->nomor_antrian, 1) + 1 : 1;
        $nomorAntrian = 'A' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $antrian = Antrian::create([
            'nomor_antrian' => $nomorAntrian,
            'status' => 'menunggu'
        ]);


        event(new AntrianDiambil($antrian));

        return response()->json($antrian);
    }


    public function panggilAntrian(Request $request, $loketId)
    {
        $loket = Loket::findOrFail($loketId);
        $antrian = Antrian::where('status', 'menunggu')
            ->orderBy('created_at')
            ->first();

        if ($antrian) {
            $antrian->update([
                'status' => 'dipanggil',
                'loket_id' => $loket->id
            ]);

            event(new AntrianDipanggil($antrian, $loket));

            return response()->json([
                'antrian' => $antrian,
                'loket' => $loket
            ]);
        }

        return response()->json(['message' => 'Tidak ada antrian'], 404);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

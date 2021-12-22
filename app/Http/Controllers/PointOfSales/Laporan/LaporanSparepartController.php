<?php

namespace App\Http\Controllers\PointOfSales\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PointOfSales\Pembayaran\PembayaranSparepartController;
use App\Model\PointOfSales\LaporanPenjualanSparepart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanSparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->pegawai->cabang == null){
            $laporan = LaporanPenjualanSparepart::with(['penjualan_sparepart.customer', 'pegawai'])
                ->where('id_bengkel', Auth::user()->id_bengkel)->where('id_cabang','=', null)->orderBy('id_laporan', 'DESC')->get();
        }else{
            $laporan = LaporanPenjualanSparepart::with(['penjualan_sparepart.customer', 'pegawai'])
                ->where('id_bengkel', Auth::user()->bengkel->id_bengkel)->where('id_cabang', Auth::user()->pegawai->cabang->id_cabang)->orderBy('id_laporan', 'DESC')->get();
        }

        return view('pages.pointofsales.laporan.laporan_sparepart', compact('laporan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $laporan = LaporanPenjualanSparepart::with([
            'penjualan_sparepart.detailsparepart'
        ])->find($id);

        return view('pages.pointofsales.laporan.detail_laporan_sparepart', compact('laporan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function CetakInvoice($id)
    {
        $laporan = LaporanPenjualanSparepart::with([
            'penjualan_sparepart.detailsparepart', 'penjualan_sparepart.Customer', 'penjualan_sparepart.Pegawai', 'penjualan_sparepart'
        ])->find($id);

        $now = Carbon::now();

        return view('print.POS.cetak-invoice-sparepart', compact('laporan', 'now'));
    }
}

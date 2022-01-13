<?php

namespace App\Http\Controllers\PointOfSales\Pembayaran;

use App\Http\Controllers\Controller;
use App\Model\Accounting\Jurnal\Jurnalpenerimaan;
use App\Model\FrontOffice\CustomerBengkel;
use App\Model\FrontOffice\Detaildiskon;
use App\Model\FrontOffice\Diskon;
use App\Model\PointOfSales\LaporanService;
use App\Model\Service\PenerimaanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->pegawai->cabang == null){
            $service_selesai = PenerimaanService::with('kendaraan', 'customer_bengkel', 'detail_sparepart', 'detail_perbaikan', 'mekanik')
                ->where('id_bengkel', Auth::user()->id_bengkel)->where('id_cabang','=', null)->where([['status', '=', 'selesai_service']])->orderBy('id_service_advisor', 'DESC')->get();
        }else{
            $service_selesai = PenerimaanService::with('kendaraan', 'customer_bengkel', 'detail_sparepart', 'detail_perbaikan', 'mekanik')
                ->where('id_bengkel', Auth::user()->bengkel->id_bengkel)->where('id_cabang', Auth::user()->pegawai->cabang->id_cabang)->where([['status', '=', 'selesai_service']])->orderBy('id_service_advisor', 'DESC')->get();
        }
       
       
        return view('pages.pointofsales.pembayaran.pembayaran_service', compact('service_selesai'));
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
    public function show($id_service_advisor)
    {
        $pembayaran_service = PenerimaanService::with('kendaraan', 'customer_bengkel', 'detail_sparepart', 'detail_perbaikan','detail_sparepart.jenissparepart.diskon.Masterdiskon', 'bengkel')->findOrFail($id_service_advisor);


        $customer = CustomerBengkel::where('id_customer_bengkel','=',$pembayaran_service->customer_bengkel->id_customer_bengkel)->get();
        
        $diskon = Diskon::where('status_diskon','=','Diskon Khusus')->first();

        // $tes = PenerimaanService::where('id_service_advisor','=', $id_service_advisor)->where('nominal_bayar', '>', $diskon->min_order);

        $tess = PenerimaanService::join('tb_pos_laporan_service', 'tb_service_advisor.id_service_advisor', 'tb_pos_laporan_service.id_service_advisor')
        ->where('tb_service_advisor.id_service_advisor','=', $id_service_advisor)
        ->where('tb_pos_laporan_service.nominal_bayar','>', $diskon->min_order)->get();

        return $tess;
        

        


        
        // return $pembayaran_service;
        return view('pages.pointofsales.pembayaran.invoice_service', compact('pembayaran_service','diskon'));
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
    public function update(Request $request, $id_service_advisor)
    {
        $status_selesai = PenerimaanService::findOrFail($id_service_advisor);
        $status_selesai->status = 'selesai_pembayaran';
        $status_selesai->update();

        $laporan_service = new LaporanService;
        $laporan_service->tanggal_laporan = Carbon::now();
        $laporan_service->id_service_advisor = $id_service_advisor;
        $laporan_service->diskon = $request->diskon;
        $laporan_service->ppn = $request->ppn;
        $laporan_service->total_tagihan = $request->total_tagihan;
        $laporan_service->nominal_bayar = $request->nominal_bayar;
        $laporan_service->kembalian = $request->kembalian;
        $laporan_service->id_pegawai = Auth::user()->pegawai->id_pegawai;
        $laporan_service->id_bengkel = Auth::user()->bengkel->id_bengkel;
        
        if(Auth::user()->pegawai->cabang != null ){
            $laporan_service->id_cabang = Auth::user()->pegawai->cabang->id_cabang;
        }else{
            
        }

        $laporan_service->save();

        $jurnal = new Jurnalpenerimaan;
        $jurnal->id_bengkel = $request['id_bengkel'] = Auth::user()->id_bengkel;
        $jurnal->id_jenis_transaksi = '10';
        $jurnal->tanggal_jurnal = Carbon::now();
        $jurnal->kode_transaksi = $status_selesai->kode_sa;
        $jurnal->tanggal_transaksi = $status_selesai->date;
        $jurnal->ref = '-';
        $jurnal->keterangan = 'Pendapatan Service';
        $jurnal->grand_total = $status_selesai->total_bayar;
        $jurnal->jenis_jurnal = 'Transaksi Service';
        $jurnal->save();

        return $request;
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
}

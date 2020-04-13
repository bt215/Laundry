<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\Pelanggan;
use App\Jeniscuci;
use App\Detailtransaksi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Auth;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;

class transaksicontroller extends Controller
{
    public function store(Request $req){
        if (Auth::user()->level=='petugas') {
            $validator=Validator::make($req->all(),[
                'id_pelanggan' => 'required',
                'id_petugas' => 'required',
                'tgl_transaksi' => 'required',
                'tgl_selesai' => 'required',
              ]);
              if($validator->fails()){
                return Response()->json($validator->errors());
              }
        
              $simpan=Transaksi::create([
                  'id_pelanggan' => $req->id_pelanggan,
                  'id_petugas' => $req->id_petugas,
                  'tgl_transaksi' => $req->tgl_transaksi,
                  'tgl_selesai' => $req->tgl_selesai
              ]);
              if($simpan){
                  $data['status']="Berhasil";
                  $data['message']="Data berhasil disimpan!";
                  return Response()->json($data);
              }else{
                  $data['status']="Gagal";
                  $data['message']="Data gagal disimpan!";
                  return Response()->json($data);
              }
        } else {
            $data['status']="Gagal";
            $data['Message']="Anda bukan Petugas!";
            return Response()->json($data);
        }
    }
    public function destroy($id){
        if(Auth::user()->level=="petugas"){
        $hapus=Transaksi::where('id_transaksi',$id)->delete();
        $status=1;
        $message="Transaksi Berhasil Dihapus";
        if($hapus){
        return Response()->json(compact('status','message'));
        }else {
        return Response()->json(['status'=>0]);
        }
    }
    else {
        return response()->json(['status'=>'anda bukan petugas']);
        }
    }

    public function show(Request $req){
        if(Auth::user()->level == "petugas"){
            $transaksi = DB::table('transaksi')->join('pelanggan','pelanggan.id_pelanggan','=','transaksi.id_pelanggan')
            ->where('transaksi.tgl_transaksi','>=',$req->tgl_transaksi)
            ->where('transaksi.tgl_transaksi','<=',$req->tgl_selesai)
            ->select('nama_pelanggan','telp','alamat','transaksi.id_transaksi','tgl_transaksi','tgl_selesai')
            ->get();
            
            if($transaksi->count() > 0){

            $data_transaksi = array();
            foreach ($transaksi as $t){
                
                $grand = DB::table('detail_transaksi')->where('id_transaksi','=',$t->id_transaksi)
                ->groupBy('id_transaksi')
                ->select(DB::raw('sum(subtotal) as grandtotal'))
                ->first();
                
                $detail = DB::table('detail_transaksi')->join('jenis_cuci','detail_transaksi.id_jenis_cuci','=','jenis_cuci.id_jenis_cuci')
                ->where('id_transaksi','=',$t->id_transaksi)
                ->get();
                

                $data_transaksi[] = array(
                    'tgl' => $t->tgl_transaksi,
                    'nama pelanggan' => $t->nama_pelanggan,
                    'alamat' => $t->alamat,
                    'telp' => $t->telp,
                    'Tanggal Ambil' => $t->tgl_selesai,
                    'Grand Total' => $grand, 
                    'Detail' => $detail,
                );
                
            }
            return response()->json(compact('data_transaksi'));
        
    }else{
            $status = 'tidak ada transaksi antara tanggal '.$req->tgl_transaksi.' sampai dengan tanggal '.$req->tgl_selesai;
            return response()->json(compact('status'));
    }
        }else{
            return Response()->json('Anda Bukan Petugas');
        }
}
public function update($id,Request $request){
    if(Auth::user()->level=="petugas"){
    $validator=Validator::make($request->all(),
        [
            'id_pelanggan' => 'required',
                'id_petugas' => 'required',
                'tgl_transaksi' => 'required',
                'tgl_selesai' => 'required',
        ]
    );

    if($validator->fails()){
    return Response()->json($validator->errors());
    }

    $ubah=Transaksi::where('id_transaksi',$id)->update([
                  'id_pelanggan' => $request->id_pelanggan,
                  'id_petugas' => $request->id_petugas,
                  'tgl_transaksi' => $request->tgl_transaksi,
                  'tgl_selesai' => $request->tgl_selesai
    ]);
    $status=1;
    $message="Transaksi Berhasil Diubah";
    if($ubah){
    return Response()->json(compact('status','message'));
    }else {
    return Response()->json(['status'=>0]);
    }
    }
else {
return response()->json(['status'=>'anda bukan petugas']);
}
}
}

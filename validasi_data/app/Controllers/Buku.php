<?php

namespace App\Controllers;

use App\Models\BukuModel;

class Buku extends BaseController
{
  protected $bukuModel;

  public function __construct()
  {
    $this->bukuModel = new BukuModel();
  }

  public function index()
  {
    //$komik = $this->komikModel->findAll();

    $data = [
      'title' => 'Daftar Buku',
      'buku' => $this->bukuModel->getBuku()
    ];


    // konek db tanpa model
    //$db = \Config\Database::connect();
    //$komik = $db->query("SELECT * FROM komik");
    //foreach ($komik->getResultArray() as $row) {
    //  d($row);
    //}

    //$komikModel = new\App\Models\KomikModels();
    //$komikModel = new KomikModel();

    return view('buku/index', $data);
  }

  public function detail($slug)
  {
    $buku = $this->bukuModel->getBuku($slug);
    $data = [
      'title' => 'Detail Buku',
      'buku' => $this->bukuModel->getBuku($slug)
    ];

    // jika komik tidak ada di tabel
    if (empty($data['buku'])) {
      throw new \Codeigniter\Exceptions\PageNotFoundException('Judul buku ' . $slug . ' tidak ditemukan.');
    }
    return view('buku/detail', $data);
  }

  public function create()
  {
    // session();
    $data = [
      'title' => 'Form Tambah Data Buku',
      'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation()
    ];

    return view('buku/create', $data);
  }

  public function save()
  {

    // validasi input
    if (
      !$this->validate([
        'judul' => [
          'rules' => 'required|is_unique[buku.judul]',
          'errors' => [
            'required' => '{field} buku harus diisi.',
            'is_unique' => '{field} buku sudah ada'
          ]
        ]
      ])
    ) {
      session()->setFlashdata('validation', \Config\Services::validation());
      return redirect()->to('/buku/create')->withInput();
      // $validation = \Config\Services::validation();
      // return redirect()->to('/buku/create')->withInput()->with('validation', $validation);
    }


    // dd($this->request->getVar());
    $slug = url_title($this->request->getVar('judul'), '-', true);
    $this->bukuModel->save([
      'judul' => $this->request->getVar('judul'),
      'slug' => $slug,
      'penulis' => $this->request->getVar('penulis'),
      'penerbit' => $this->request->getVar('penerbit'),
      'sampul' > $this->request->getVar('sampul')
    ]);

    session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');

    return redirect()->to('/buku');

  }

  public function delete($id)
  {
    $this->bukuModel->delete($id);
    session()->setFlashdata('pesan', 'Data berhasil dihapus');
    return redirect()->to('/buku');
  }
}
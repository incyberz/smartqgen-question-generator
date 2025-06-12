<?php
$default_nama_paket_mapel = "Paket $last_mapel $d[nama_jenjang]";
$urutan = $last_urutan + 1;

# ============================================================
# OPTION KELAS
# ============================================================
$option_kelas = '';
$jenjang['max_kelas'] = $jenjang['max_kelas'] ?? $jenjang['min_kelas'];
for ($i = $jenjang['min_kelas']; $i <= $jenjang['max_kelas']; $i++) {
  $option_kelas .= "<option value=$i>Kelas $i</option>";
}

$tr_form = "
  <tr>
    <td>$last_mapel</td>
    <td colspan=100%>
      
      <form method=post class='flexy gap1' >
        <div class=hideita>
          <input required name=urutan value=$urutan type=hidden placeholder='Urutan...'>
          $urutan.
        </div>
        <div class='btn-aksi pointer' id=blok-form-add-materi-$last_id--toggle>➕</div>

        <div class='hideit' id=blok-form-add-materi-$last_id>
          <div class='flexy gap2'>
            <div>
              <input required name=nama_materi type=text placeholder='Nama materi ...'>
            </div>
            <div>
              <select required name=kelas>
                <option value=''>--pilih kelas--</option>
                $option_kelas
              </select>
            </div>
            <div>
              <button name=btn_add_materi value=$last_id_mapel>Add Materi $last_mapel</button>
            </div>
          </div>
        </div>
      </form>

    </td>
  </tr>
  <tr>
    <td colspan=100%>
      <span class='btn-aksi hover green ' id=form-tambah-paket-$d[id]--toggle>➕ Paket $last_mapel</span>
      <div class='hideit' id=form-tambah-paket-$d[id]>
        <form method=post class='flexy gap1 mt2' id=form-tambah-paket-$d[id]>
          <div>
            <input 
              type=text
              required 
              id=nama_paket 
              name=nama_paket 
              minlength='3' 
              maxlength='50' 
              placeholder='Nama Paket $last_mapel...'
              value='$default_nama_paket_mapel'
            />
          </div>
          <div>
            <input 
              type=text
              required 
              id=singkatan 
              name=singkatan 
              minlength='2' 
              maxlength='10' 
              placeholder='Singkatan...'
            />
          </div>
          <div>
            <button name=btn_add_paket_mapel id=btn_add_paket_mapel value='$last_id_mapel--$get_jenjang'>Add Paket $last_mapel</button>
          </div>
        </form>    
      </div>    
    </td>    
  </tr>    
";

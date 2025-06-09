<?php
$default_nama_paket_mapel = "Paket $last_mapel $d[nama_jenjang]";

$tr_form = "
  <tr>
    <td colspan=100%>
      <span class='btn-aksi hover green ' id=form-tambah-paket-$d[id]--toggle>➕ Paket $last_mapel</span>
      <div class='hideita' id=form-tambah-paket-$d[id]>
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

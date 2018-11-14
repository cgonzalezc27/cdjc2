function moficiarRol($id_rol, $nombre){
  $db = connectDb();
  if ($db != NULL) {
    $query = 'DELETE FROM privilegio_rol WHERE id_role="'.$id_rol.'"';
    $db->query($query);
    $query2 = 'UPDATE rol SET nombre="'.$nombre.'" WHERE id_role="'.$id_rol.'"';
    $db->query($query2);
    disconnectDb($db);
    return true;
  }else {
    return false;
  }
}
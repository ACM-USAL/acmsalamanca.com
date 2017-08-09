<?php foreach($errors as $error): ?>
    <?php echo $error ?><br>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data" >
  <p>Si estás interesado en formar parte del grupo ACM USAL rellena el formulario y nos pondremos en contacto contigo.</p>
  <p>Los campos marcados con * son necesarios.</p>
    
  <p>



 <div class="row">
  <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" >
  <label for="name">
      <span>Nombre *: </span>
      <input type="text" name="name" id="name" size="30" />
      <div id="imagen_mostrar"></div>
    </label>
</div>
  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" >
 
</div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="overflow:hidden;">
   <label for="imagen">
      <span>Imagen de perfil *: </span>
      <input id ="imagen_campo" name="imagen_campo" type="file"  accept="image/*" size="30"/>
    </label>
  </div>
</div>





 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    
  <p>
    <label for="surname">
      <span>Apellidos *: </span>
      <input type="text" name="surname" id="surname" size="30"/>
    </label>
  </p>
    
</div>
</div>

 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
  <p>
    <label for="mail">
      <span>Email *: </span>
      <input type="email" name="mail" id="mail" size="30"/>
    </label>
  </p>
  </div>
</div>

 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
  <p>
    <label for="occupation">
      <span>Ocupacion *: </span>
        <select name="occupation">
    <option value="Estudiante Universitario">Estudiante Universitario</option>
    <option value="Estudiante">Estudiante</option>
    <option value="Trabajador">Trabajador</option>
    <option value="Docente">Docente</option>
    <option value="Otro">Otro</option>
  </select>
    </label>
  </p>
</div>
</div>
    
 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
  <p>
    <label for="phone">
      <span>Número de teléfono / id de telegram: </span>      <p>Utilizamos telegram como medio de comunicacion entre los miembros de la asociacion y para registrarse en telegram hace falta un numero de telefono, aunque si lo prefieres puedes introducir el usuario directamente.</p>
      <input type="text" name="phone" id="phone" size="30"/>
    </label>
  </p>
</div>
</div>
    
 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
  <p>
    <label for="bio">
      <span>Biografía</span>
      <div>En usal.acm.org/nosotros los integrantes del capítulo hemos incluido una pequeña descripción de cada uno de nosotros, cuéntanos algo sobre ti para incluirlo.</div>
      <textarea name="bio" id="bio" size="300"></textarea>
    </label>
  </p>
</div>
</div>
  
  <p>
 <div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
  <p>
    <label for="comments">
      <span>Comentarios</span>
      <div>Si quieres indicarnos tu cuenta de Twitter o página web personal para la biografía, proyectos personales, ideas... o cualquier otra cosa hazlo aquí.</div>
      <textarea name="comments" id="comments" size="300"></textarea>
    </label>
  </p>
</div>
</div>
  <p>
    <input type="submit" name="register-submit" value="Enviar" />
  </p>
 </form>


  <script>
              function archivo(evt) {
                  var files = evt.target.files; // FileList object
             
                  // Obtenemos la imagen del campo "file".
                  for (var i = 0, f; f = files[i]; i++) {
                    //Solo admitimos imágenes.
                    if (!f.type.match('image.*')) {
                        continue;
                    }
             
                    var reader = new FileReader();
             
                    reader.onload = (function(theFile) {
                        return function(e) {
                          // Insertamos la imagen
                         document.getElementById("imagen_mostrar").innerHTML = ['<img  style="margin:2em;width:300px;height:300px;"class="thumb" src="', e.target.result,'" title="', escape(theFile.name), '"/>'].join('');
                        };
                    })(f);
             
                    reader.readAsDataURL(f);
                  }
              }
           
              document.getElementById('imagen_campo').addEventListener('change', archivo, false);
      </script>

### Clase Principal (AIEntries): 


Esta clase se utiliza para inicializar el plugin, establecer ganchos (hooks) y gestionar la activación y desactivación del plugin (para planificar/desprogramar la tarea cron).

### Clase de Configuración (AIEntries_Settings): 


Esta clase maneja la creación de la página de configuración en el área de administración de WordPress.

### Clase de API (AIEntries_API): 


Esta clase maneja todas las llamadas a las APIs externas y la creación de entradas en WordPress, incluyendo la generación de imágenes y la gestión de archivos base64.

### Clase de Cron (AIEntries_Cron): 


Esta clase se encarga de las tareas programadas, ejecutando una función en un intervalo diario para crear nuevas publicaciones basadas en la configuración proporcionada.

### Página de Configuración (settings-page.php): 


Este archivo contiene el HTML necesario para la página de configuración del plugin en el área de administración de WordPress.
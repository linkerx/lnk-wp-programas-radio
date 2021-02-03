<?php

/**
 Plugin Name: LNK Programas para Radio Encuentro
 Plugin URI: https://github.com/linkerx/lnk-wp-programas-radio
 Description: Tipo de Dato Programa para Wordpress
 Version: 1
 Author: Diego
 Author URI: https://linkerx.com.ar/
 License: GPL2
 */

/**
 * Genera el tipo de dato formulario
 */
function lnk_programa_create_type(){
    register_post_type(
        'programa',
        array(
            'labels' => array(
                'name' => __('Programas','programas_name'),
                'singular_name' => __('Programa','programas_singular_name'),
                'menu_name' => __('Programación','programas_menu_name'),
                'all_items' => __('Todos los programas','programas_all_items'),
            ),
            'description' => 'Programacion de Radio',
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 8,
            'support' => array(
                'title',
                'excerpt',
                'editor',
                'thumbnail',
                'revisions'
            ),
            "capability_type" => 'programas',
            "map_meta_cap" => true
        )
    );
}
add_action('init', 'lnk_programa_create_type');
add_post_type_support('programa', array('thumbnail','excerpt'));

function lnk_programa_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'programa') return false;
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'lnk_programa_disable_gutenberg', 10, 2);

/**
* Taxonomias para programas
*/

/*
function lnk_register_programa_taxonomies(){

    $labels = array(
        'name' => "Generos",
        'singular_name' => "Genero",
    );
    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug'=>'genero'),
    );
    register_taxonomy('genero','programa',$args);
}
add_action( 'init', 'lnk_register_programa_taxonomies');
*/

/**
 * agrega columnas al listado de formularios
 */
function lnk_programa_add_columns($columns) {
    global $post_type;
    if($post_type == 'programa'){
        $columns['lnk_programa_horarios'] = "Horarios";
        $columns['lnk_programa_conduccion'] = "Conducción";
        $columns['lnk_programa_produccion'] = "Producción";
        $columns['lnk_programa_operacion'] = "Operación/Musicalización";
    }
    return $columns;
}
add_filter ('manage_posts_columns', 'lnk_programa_add_columns');

function lnk_programa_show_columns_values($column_name) {
    global $wpdb, $post;
    $id = $post->ID;

    if($post->post_type == 'programa'){
        $id = $post->ID;
        if($column_name === 'lnk_programa_horarios'){
            if(!empty(get_post_meta($id,'lnk_programa_dias_1',true))) {
                print get_post_meta($id,'lnk_programa_dias_1',true)." - ".get_post_meta($id,'lnk_programa_horarios_1',true);
            }
            if(!empty(get_post_meta($id,'lnk_programa_dias_2',true))) {
                print ", ".get_post_meta($id,'lnk_programa_dias_2',true)." - ".get_post_meta($id,'lnk_programa_horarios_2',true);
            }
        } elseif($column_name === 'lnk_programa_conduccion'){
            print get_post_meta($id,'lnk_programa_conduccion',true);
        } elseif($column_name === 'lnk_programa_produccion'){
            print get_post_meta($id,'lnk_programa_produccion',true);
        } elseif($column_name === 'lnk_programa_operacion'){
            print get_post_meta($id,'lnk_programa_operacion',true);
        }
    }
}
add_action ('manage_posts_custom_column', 'lnk_programa_show_columns_values');

/**
 * Agrega los hooks para los datos meta en el editor de programas
 */
function lnk_programa_custom_meta() {
    global $post;
    if($post->post_type == 'programa'){
        add_meta_box('lnk_programa_horarios',"Horarios", 'lnk_programa_horarios_meta_box', null, 'normal','core');
        add_meta_box('lnk_programa_equipo',"Equipo", 'lnk_programa_equipo_meta_box', null, 'normal','core');
        add_meta_box('lnk_programa_redes',"Redes Sociales", 'lnk_programa_redes_meta_box', null, 'normal','core');
    }
}
add_action ('add_meta_boxes','lnk_programa_custom_meta');

function lnk_programa_horarios_meta_box() {
    global $post;

    $dias_1 = get_post_meta( $post->ID, 'lnk_programa_dias_1', true );
    $horarios_1 = get_post_meta( $post->ID, 'lnk_programa_horarios_1', true );
    $dias_2 = get_post_meta( $post->ID, 'lnk_programa_dias_2', true );
    $horarios_2 = get_post_meta( $post->ID, 'lnk_programa_horarios_2', true );
    
    $html = "<div class='horarios_group_container' style='margin: 5px;'>";
    $html .= "Dias: <input type='text' id='lnk_programa_dias_1' name='lnk_programa_dias_1' value='".$dias_1."' size='15' style='margin: 5px;'>";
    $html .= "Horario: <input type='text' id='lnk_programa_horarios_1' name='lnk_programa_horarios_1' value='".$horarios_1."' size='15' style='margin: 5px;'>";
    $html .= "</div>";
    $html .= "<div class='horarios_group_container' style='margin: 5px;'>";
    $html .= "Dias: <input type='text' id='lnk_programa_dias_2' name='lnk_programa_dias_2' value='".$dias_2."' size='15' style='margin: 5px;'>";
    $html .= "Horario: <input type='text' id='lnk_programa_horarios_2' name='lnk_programa_horarios_2' value='".$horarios_2."' size='15' style='margin: 5px;'>";
    $html .= "</div>";
    echo $html;
}

function lnk_programa_equipo_meta_box() {
    global $post;

    $conduccion = get_post_meta( $post->ID, 'lnk_programa_conduccion', true );
    $produccion = get_post_meta( $post->ID, 'lnk_programa_produccion', true );
    $operacion = get_post_meta( $post->ID, 'lnk_programa_operacion', true );
    
    $html = "<div class='equipo_container'>";
    
    $html .= "<div class='equipo_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_conduccion'>Conducción: </label>";
    $html .= "<input type='text' id='lnk_programa_conduccion' name='lnk_programa_conduccion' value='".$conduccion."' size='30' style='margin: 5px;'>";
    $html .= "</div>";

    $html .= "<div class='equipo_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_produccion'>Producción: </label>";
    $html .= "<input type='text' id='lnk_programa_produccion' name='lnk_programa_produccion' value='".$produccion."' size='30' style='margin: 5px;'>";
    $html .= "</div>";

    $html .= "<div class='equipo_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_operacion'>Operación/Musicalización: </label>";
    $html .= "<input type='text' id='lnk_programa_operacion' name='lnk_programa_operacion' value='".$operacion."' size='30' style='margin: 5px;'>";
    $html .= "</div>";

    $html .= "</div>";
    echo $html;
}


function lnk_programa_redes_meta_box() {
    global $post;

    $redes_facebook = get_post_meta( $post->ID, 'lnk_programa_redes_facebook', true );
    $redes_instagram = get_post_meta( $post->ID, 'lnk_programa_redes_instagram', true );
    $redes_youtube = get_post_meta( $post->ID, 'lnk_programa_redes_youtube', true );
    $redes_twitter = get_post_meta( $post->ID, 'lnk_programa_redes_twitter', true );
    $redes_whatsapp = get_post_meta( $post->ID, 'lnk_programa_redes_whatsapp', true );
    $redes_telegram = get_post_meta( $post->ID, 'lnk_programa_redes_telegram', true );
        
    $html = "<div class='redes_container'>";
    
    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_facebook'>Facebook: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_facebook' name='lnk_programa_redes_facebook' value='".$redes_facebook."' size='30' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Dirección completa al perfil/página/grupo)</span>";
    $html .= "</div>";

    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_instagram'>Instagram: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_instagram' name='lnk_programa_redes_instagram' value='".$redes_instagram."' size='30' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Dirección completa al perfil)</span>";
    $html .= "</div>";

    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_youtube'>Youtube: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_youtube' name='lnk_programa_redes_youtube' value='".$redes_youtube."' size='30' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Dirección completa al canal/playlist)</span>";
    $html .= "</div>";

    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_twitter'>Twitter: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_twitter' name='lnk_programa_redes_twitter' value='".$redes_twitter."' size='30' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Dirección completa al perfil)</span>";
    $html .= "</div>";

    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_whatsapp'>Whatsapp: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_whatsapp' name='lnk_programa_redes_whatsapp' value='".$redes_whatsapp."' size='20' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Número de teléfono)</span>";
    $html .= "</div>";

    $html .= "<div class='redes_input' style='margin: 5px;'>";
    $html .= "<label for='lnk_programa_redes_telegram'>Telegram: </label>";
    $html .= "<input type='text' id='lnk_programa_redes_telegram' name='lnk_programa_redes_telegram' value='".$redes_telegram."' size='20' style='margin: 5px;'>";
    $html .= "<span class='redes_descr' style='font-size:0.8em;color:#555;'>(Nombre de Usuario)</span>";
    $html .= "</div>";

    $html .= "</div>";
    echo $html;
}


function lnk_programa_save_post_meta($id) {
    global $post_type;
    if($post_type == 'programa'){
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return $id;
        if (defined('DOING_AJAX') && DOING_AJAX)
                return $id;

        // horarios
        if(isset($_POST['lnk_programa_dias_1']))
            update_post_meta($id, 'lnk_programa_dias_1', $_POST['lnk_programa_dias_1']);
        if(isset($_POST['lnk_programa_horarios_1']))            
            update_post_meta($id, 'lnk_programa_horarios_1', $_POST['lnk_programa_horarios_1']);
        if(isset($_POST['lnk_programa_dias_2']))
            update_post_meta($id, 'lnk_programa_dias_2', $_POST['lnk_programa_dias_2']);
        if(isset($_POST['lnk_programa_horarios_2']))
            update_post_meta($id, 'lnk_programa_horarios_2', $_POST['lnk_programa_horarios_2']);
        // equipo
        if(isset($_POST['lnk_programa_conduccion']))
            update_post_meta($id, 'lnk_programa_conduccion', $_POST['lnk_programa_conduccion']);
        if(isset($_POST['lnk_programa_produccion']))
            update_post_meta($id, 'lnk_programa_produccion', $_POST['lnk_programa_produccion']);
        if(isset($_POST['lnk_programa_operacion']))
            update_post_meta($id, 'lnk_programa_operacion', $_POST['lnk_programa_operacion']);
        // redes
        if(isset($_POST['lnk_programa_redes_facebook']))
            update_post_meta($id, 'lnk_programa_redes_facebook', $_POST['lnk_programa_redes_facebook']);
        if(isset($_POST['lnk_programa_redes_instagram']))
            update_post_meta($id, 'lnk_programa_redes_instagram', $_POST['lnk_programa_redes_instagram']);
        if(isset($_POST['lnk_programa_redes_youtube']))
            update_post_meta($id, 'lnk_programa_redes_youtube', $_POST['lnk_programa_redes_youtube']);
        if(isset($_POST['lnk_programa_redes_twitter']))
            update_post_meta($id, 'lnk_programa_redes_twitter', $_POST['lnk_programa_redes_twitter']);
        if(isset($_POST['lnk_programa_redes_whatsapp']))
            update_post_meta($id, 'lnk_programa_redes_whatsapp', $_POST['lnk_programa_redes_whatsapp']);
        if(isset($_POST['lnk_programa_redes_telegram']))
            update_post_meta($id, 'lnk_programa_redes_telegram', $_POST['lnk_programa_redes_telegram']);
   }
}
add_action('save_post','lnk_programa_save_post_meta');

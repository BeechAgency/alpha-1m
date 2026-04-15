
<?php 
    $church_file = get_field('church_names', 'options');
    $church_data = [];

    if ($church_file) {
        $file_url = $church_file['url'];
        $file_path = download_url($file_url);

        if (is_wp_error($file_path)) {
            error_log('Download failed: ' . $file_path->get_error_message());
            
        } elseif (file_exists($file_path)) {
            if (($handle = fopen($file_path, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $church_data[] = $row[0];
                }
                fclose($handle);
            }
        }

        /*
        error_log("Church file URL: " . $file_url);
        error_log("Resolved file path: " . $file_path);
        error_log("File exists? " . (file_exists($file_path) ? 'yes' : 'no'));
        error_log("church_file: " . print_r($church_file, true));
        */
    }

    

    if(!empty($church_data)):

        shuffle($church_data);

        echo '<div class="church-names"><div class="church-names__position"><div class="church-names__inner">';
        foreach($church_data as $item):
            echo '<span class="church">' . $item . '</span> • ';
        endforeach;

        shuffle($church_data);

        foreach($church_data as $item):
            echo '<span class="church">' . $item . '</span> • ';
        endforeach;
        echo '</div></div></div>';
    endif;
?>


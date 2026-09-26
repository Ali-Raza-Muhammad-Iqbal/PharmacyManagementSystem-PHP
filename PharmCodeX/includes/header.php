<!-- header.php -->
<header style="position: fixed; top: 0; left: 220px; right: 0; height: 60px; background-color: #0d6efd; color: #fff; display: flex; align-items: center; padding: 0 20px; z-index: 1000;">
    <h5 class="m-0">
        <?php 
            // Dynamically show page title
            if(isset($pageTitle)) echo $pageTitle; 
            else echo "Dashboard"; 
        ?>
    </h5>
</header>

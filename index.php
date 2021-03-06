<?php
$pageTitle = 'Home';
$pageStyles = ['style-index.css'];
require_once 'partials/head.php';
require_once 'partials/header.php';
require_once 'src/common-utils.php';
// require_once 'src/classes/Index.php';

// Always start a new session on page reload
(session_status() === PHP_SESSION_NONE ? session_start() : $_SESSION = []);
// $index = new Index;
?>

<main>
  <?php require 'partials/navi.html'; ?>

  <section>
    <p>Welcome to the <a href="https://www.bricksinmotion.com">Bricks in Motion</a> film directory archive! Here, you will find an archive of all the brickfilms submitted to our former directory. Feel free to explore our collection!</p>
    <p>Is there a specific film you're wanting? <a href="search.php">Search for it</a>!</p>
  </section>

  <section>
   <h2>View films by year</h2>
    <div class="area-filter-year">
  <?php
    // foreach ($index::get_film_years() as $year):
    //   echo "<span class='btn-filter-year' data-year='{$year}'>{$year}</span>";
    // endforeach;
  ?>
  </div>
  </section>

  <section class="area-film-list">
    <h2 class="film-year"></h2>
    <div></div>
  </section>
</main>

<?php require 'partials/footer.php'; ?>
<script src="js/on-resize.js"></script>
<script src="js/index.js"></script>
</body>
</html>

<?php
require_once 'src/common-utils.php';
require_once 'src/classes/Film.php';

// Does the user wants a JSON representation of the film?
$client_wants_json = (isset($_GET["format"]) && $_GET["format"] === 'json');

// Attempt to load this film
$film_id = escape_xss($_GET['film_id']);
$film = new Film($film_id);

// Get out of here if the film doesn't exist OR
// this is a JSON request for a non Reborn-style film,
//which is not supported
if ($film->get_film_exists() === false ||
    ($client_wants_json && $film->get_film_reborn_status() === false)
) {
    // Handle this according to the request type
    if ($client_wants_json) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['exists' => false]);
        exit();
    } else {
        redirect_url('404.php');
    }
}

// Build up the JSON response and send it back
if ($client_wants_json) {
    require_once 'src/film-json-response.php';
    header('Content-Type: application/json');
    echo json_encode(build_film_json($film));
    exit();
}

// Get film and director info
$film_info = $film->get_film_info();
$director = $film->get_director_info();

// Set the page info
$pageTitle = $film_info->title;
$pageStyles = ['style-film.css'];

// Start loading the page
require_once 'partials/head.php';
require_once 'partials/header.php';
?>

<main>
  <?php require 'partials/navi.html'; ?>

  <section class="film-info">
    <div class="thumbnail">
      <img class="film-thumbnail" alt="<?= htmlspecialchars($film_info->title); ?>" src="/film-images/<?= $film_info->thumbnail; ?>">
    </div>

    <div class="details">
      <h2><?= $film_info->title; ?></h2>
      <span><strong>Directed By</strong>: <a href="/director.php?director_id=<?= $director->user_id; ?>"><?= $director->real_name; ?> <small>(<?= $director->user_name; ?>)</small></a></span><br>
      <span><strong>Released</strong>: <?= format_film_release_date($film_info->release_date); ?></span><br>
      <span><strong>Runtime</strong>: <?= format_film_runtime($film_info->length); ?></span><br>
      <div class="film-genres"><strong>Genres</strong>:
        <?php
        foreach ($film->get_genres() as $genre):
          echo "<span class='genre'>{$genre}</span>";
        endforeach;
        ?>
      </div>
      <?php $film_rating = $film->get_rating(); ?>
      <span><strong>Rating</strong>: <?= $film_rating->rating; ?>/5 (out of <?= $film_rating->total_votes; ?> <?= $film_rating->word; ?>)</span><br>
      <div class="film-warnings"><strong>Content Advisory</strong>:
        <?php
        foreach ($film->get_advisories() as $advisory):
          $str = "<span class='warning |severity|'>|capital_severity| |type|</span>";
          $str = str_replace('|severity|', $advisory['severity'], $str);
          $str = str_replace('|capital_severity|', ucfirst($advisory['severity']), $str);
          $str = str_replace('|type|', $advisory['type'], $str);
          echo $str;
        endforeach;
        ?>
      </div>
      <?php
        $forum_topic = $film->get_forum_topic();
        if ($forum_topic !== false):
      ?>
      <span>
        <strong><a href="http://www.bricksinmotion.com/forums/topic/<?= $forum_topic->topic_id; ?>/">Forum Topic</a></strong>
      </span>
      <?php endif; ?>
    </div>

    <section class="film-links">
      <h3>Watch</h3>
      <div>
        <?php
        foreach ($film->get_links() as $record):
          echo "<span class='link'><a href='{$record->link}' target='_blank'>{$record->label}</a></span>";
        endforeach;
        ?>
      </div>
    </section>
  </section>

  <section class="film-desc">
    <h3>Director's Description</h3>
    <blockquote><?= convert_bb_code($film_info->desc); ?></blockquote>
  </section>

  <section class="film-critiques">
    <div class="film-cast-crew">
      <h3>Cast &amp; Crew</h3>
      <?php
        foreach ($film->get_cast_crew() as $job):
          // Break up the crew name for people with multiple roles
          $job_crewname = str_replace('/', '<br>', $job->crewname, $count);
          echo "<div>
            <a href='director.php?director_id={$job->cc_user_id}'>{$job->name}</a>
            <span class='crewname'>{$job_crewname}</span>
          </div>";

          // For every line we had to break, shift the next role down
          // to keep everything aligned
          echo str_repeat('<br>', $count);
        endforeach;
        ?>
    </div>

    <div class="film-honors">
      <h3>Honors</h3>
      <span><?= $film->get_honors(); ?></span>
    </div>

    <div class="film-staff-ratings">
      <h3>Staff Ratings</h3>
      <?php
        foreach ($film->get_staff_ratings() as $sr):
          echo "<div class='{$sr->class}'>
            <strong>{$sr->category}</strong>
            <span>{$sr->rating}</span>
          </div>";
        endforeach;
        ?>
    </div>
  </section>

  <section class="film-reviews">
    <h3>Staff Reviews</h3>
    <?php
    foreach ($film->get_reviews() as $record): ?>
    <blockquote>
      <strong><?= $record->real_name; ?></strong>
      <p><?= convert_bb_code($record->comments); ?></p>
    </blockquote>
    <?php endforeach; ?>
  </section>
</main>

<?php require 'partials/footer.php'; ?>
</body>
</html>

<h1>Page de l'article !!!!</h1>

<div>
    <p>              <?= $data->getTitle();       ?> </p>
    <p> Published on <?= $data->getDateContents() ?> </p>
    <p>              <?= $data->getAuthor();      ?> </p>
    <p>              <?= $data->getContents();    ?> </p>
    <p> <a href="#comment">Add Comment</a>           </p>
</div>

<!-- Show Comments -->

<?php foreach ($data->getCommentsList() as $comment) { ?>

    <div>
        <p> <?= $comment->getTitle()        ?> </p>
        <p> <?= $comment->getDateContents() ?> </p>
        <p> <?= $comment->getAuthor()       ?> </p>
        <p> <?= $comment->getContents()     ?> </p>
    </div>

<?php } ?>

<!-- Add Comments -->

<form action="/addComment" method="post">
  <fieldset id="comment">
    <legend>Something to say?</legend>
    <p>
        <label>Nickname</label><br>
        <input type="text" name="nickname" placeholder="e.g Alex" required>
    </p>
    <p>
        <label>Your message</label><br>
        <textarea rows="5" cols="40" placeholder="e.g My name is Alex" required></textarea>
    </p>
    <button type="submitt">Add your comment</button>
    <button type="reset">Reset</button>
  </fieldset>
</form>
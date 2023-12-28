<?php
/**
 *
 */

?>

<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 10rem;">

  <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">Rivus</span>
  </a>

  <hr>

  <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="/" class="nav-link active" aria-current="page">
          /[root]
        </a>
      </li>
      <li class="nav-item">
        <a href="/home" class="nav-link">
          Home
        </a>
      </li>
      <li>
        <a href="/incoming" class="nav-link text-white">
          Incoming
        </a>
      </li>
      <li>
        <a href="/outgoing" class="nav-link text-white">
          Outgoing
        </a>
      </li>
      <li>
        <a href="/config" class="nav-link text-white">
          Config
        </a>
      </li>
  </ul>

  <hr>

  <div class="dropdown">
    <a href="/ping" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://github.com/edoceo.png" alt="" width="32" height="32" class="rounded-circle me-2">
      <strong>/ping</strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="">
      <li><a class="dropdown-item" href="#">New project...</a></li>
      <li><a class="dropdown-item" href="#">Settings</a></li>
      <li><a class="dropdown-item" href="#">Profile</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="#">Sign out</a></li>
    </ul>
  </div>

</div>

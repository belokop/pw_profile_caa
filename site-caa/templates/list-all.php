<?php namespace ProcessWire;

/**
 * Outputs list of all artworks, regardless of collection
 *
 */

region('content', renderObjectList(findObjects('','artwork'),2));


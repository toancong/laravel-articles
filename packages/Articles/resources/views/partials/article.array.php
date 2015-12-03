<?php

$this->extract($category, [
    'id',
    'title',
    'content',
    'alias',
    'image',
    'description',
    'order',
    'status',
]);

$this->set('parent', ['id' => $category->parent_id]);

$this->set('createdAt', date('c', strtotime($category->created_at)));
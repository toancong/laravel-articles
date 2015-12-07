<?php

namespace PhpSoft\Articles\Models;

use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

class Article extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';

    const STATUS_ENABLE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'category_id', 'title', 'content', 'alias', 'image', 'description', 'order'];

    /**
     * Make relationship to category.
     *
     * @return relationship
     */
    public function category()
    {
        return $this->belongsTo('PhpSoft\Articles\Models\Category', 'category_id'); // @codeCoverageIgnore
    }

    /**
     * Create the model in the database.
     *
     * @param  array  $attributes
     * @return category
     */
    public static function create(array $attributes = [])
    {
        if (empty($attributes['alias'])) {
            $attributes['alias'] = Str::slug($attributes['title'])
                .'-'. Uuid::generate(4);
        }

        $attributes['user_id'] = Auth::user()->id;

        return parent::create($attributes)->fresh();
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @return bool|int
     */
    public function update(array $attributes = [])
    {
        if (isset($attributes['alias']) && empty($attributes['alias'])) {
            $title = $this->title;

            if (isset($attributes['title'])) {
                $title = $attributes['title'];
            }

            $attributes['alias'] = Str::slug($title)
                .'-'.Uuid::generate(4);
        }

        if (!parent::update($attributes)) {
            throw new Exception('Cannot update article.');
        }

        return $this;
    }

    /**
     * set status enable
     * @return boolean
     */
    public function enable()
    {
        $this->status = $this->status | Article::STATUS_ENABLE;
        return $this->save();
    }

    /**
     * set status disable
     * @return boolean
     */
    public function disable()
    {
        $this->status = $this->status & ~Article::STATUS_ENABLE;
        return $this->save();
    }

    /**
     * check status enable
     * @return boolean [description]
     */
    public function isEnable()
    {
        return Article::STATUS_ENABLE == ($this->status & Article::STATUS_ENABLE);
    }
}

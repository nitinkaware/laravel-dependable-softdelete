<?php

namespace NitinKaware\DependableSoftDeletable\Traits;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait DependableDeleteTrait {

    /**
     * Booted on deleting event.
     */
    public static function bootDependableDeleteTrait()
    {
        static::deleting(function ($model) {
            if ( !self::softDeleteTraitExits($model)) {
                throw new Exception("Model must uses SoftDeletes.");
            }

            foreach (static::getDeletableRelationships() as $relationship) {
                $model->deleteDependableModels($model->{$relationship}());
            }
        });
    }

    /**
     * Detect the type of the relationship and delete model.
     *
     * @param $relationship
     */
    public function deleteDependableModels($relationship)
    {
        switch (true) {
            case $relationship instanceof HasOne || $relationship instanceof HasMany:
                $this->deleteDependableHasOneOrMany($relationship);
                break;
            case $relationship instanceof BelongsToMany:
                $this->deleteDependableBelongsToMany($relationship);
                break;
            case $relationship instanceof BelongsTo:
                $this->deleteDependableBelongsTo($relationship);
                break;
        }
    }

    /**
     * Delete dependable HasOne or HasMany relationship.
     *
     * @param $relationship
     *
     * @return bool
     */
    public function deleteDependableHasOneOrMany($relationship)
    {
        $related = $relationship->getModel();

        if (isset($this->toDelete)) {
            $relatedQuery = $related->whereIn(
                $relationship->getForeignKeyName(),
                $this->getDeletableForeignKeys()
            );

            $this->setDeletableForeignKeys(
                $relatedQuery->get()->pluck('id')->toArray(),
                $related
            );

            $relatedQuery->delete();
        } else {
            $this->setDeletableForeignKeys(
                $relationship->get()->pluck($related->getKeyName())->toArray(),
                $related
            );
            $relationship->delete();
        }

        // Once we delete the current model, check if the current model has any SoftDelete
        // and DependableDeleteTrait. If has, then it means that used want to delete
        // that relations too. In that case we will just do as they wish.
        if ($related->fireModelEvent('deleting') === false) {
            return false;
        }
    }

    /**
     * Delete dependable belongsToMany relationship.
     *
     * @param $relationship
     *
     * @return bool if fails.
     */
    public function deleteDependableBelongsToMany($relationship)
    {
        $belongsToMany = $relationship;

        if ( !$pivotInstance = $belongsToMany->first()) {
            return false;
        }

        $pivotRelation = $pivotInstance->pivot;

        $foreignKeyName = $this->guessForeignKeyName($belongsToMany);

        $pivotRelation->newQuery()->whereIn(
            $foreignKeyName,
            $this->getForeignKeyIds($belongsToMany, $foreignKeyName))->update(['deleted_at' => DB::raw('NOW()')]
        );
    }

    /**
     * Delete dependable belongsTo relationship.
     *
     * @param $relationship
     */
    public function deleteDependableBelongsTo($relationship)
    {
        $relationship->delete();
    }

    /**
     * Get the relationships defined on the model.
     *
     * @return mixed
     * @throws Exception
     */
    public static function getDeletableRelationships()
    {
        if (isset(static::$dependables) && is_array(static::$dependables)) {
            return static::$dependables;
        }

        throw new Exception("No dependable relationship provided.");
    }

    /**
     * Check if the model contain SoftDeletes trait.
     *
     * @param $model
     *
     * @return bool
     */
    protected static function softDeleteTraitExits($model)
    {
        return collect(class_uses_recursive($model))->values()->map(function ($value) {
            return class_basename($value);
        })->contains('SoftDeletes');
    }

    /**
     * Returns the pivot table id for BelongsToMany Relationship.
     *
     * @param $belongsToMany
     *
     * @return array
     */
    protected function getForeignKeyIds($belongsToMany, $foreignKeyName)
    {
        return $belongsToMany->get()
            ->pluck('pivot')
            ->pluck($foreignKeyName)
            ->toArray();
    }

    /**
     * Returns the name of the foreign key for the model.
     *
     * @param $belongsToMany
     *
     * @return string Foreign Key Name
     * @throws Exception
     */
    public function guessForeignKeyName($belongsToMany)
    {
        $qualifiedName = $belongsToMany->getQualifiedForeignKeyName();

        if ( !Str::contains($qualifiedName, '.')) {
            throw new Exception("Unable to guess foreign key name.");
        }

        return last(explode('.', $qualifiedName));
    }

    /**
     * Sets the deletable foreign keys.
     *
     * @param array $ids
     * @param null $model
     */
    protected function setDeletableForeignKeys(array $ids = [], $model = null)
    {
        $model = $model ?: $this;

        $model->toDelete = $ids;
    }

    /**
     * Get the deletable foreign keys.
     *
     * @return mixed
     */
    protected function getDeletableForeignKeys()
    {
        return $this->toDelete;
    }
}
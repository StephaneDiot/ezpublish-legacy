{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{default class='warning'}
    {section show=and($validation.processed,$collection_attributes)}
        {section show=$validation.attributes}

          <div class="{$class|wash}">
            <h2>{"Missing or invalid input"|i18n("design/standard/node/view")}</h2>
          <ul>
          {section name=UnvalidatedAttributes loop=$validation.attributes show=$validation.attributes}
            <li>{$:item.name|wash}: {$:item.description}</li>
          {/section}
          </ul>
          </div>

        {/section}

    {/section}
{/default}
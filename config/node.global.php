<?php
return [
    'node' => [
        /**
         * Extends nodes, with functionality concerning SEO-relevant information.
         * 
         * Defaults to true.
         * 
         * @var boolean
         */
        'enable_meta_tags' => true,
        
        /**
         * If set to true, whenever a node is accessed, a counter for this node is incremented.
         * This is a minimal statistical feature. You can see which nodes a most relevant for your users.
         * Not only the number of accesses to a node is stored, but also the timestamp of the last access.
         * 
         * A typical use case would be to see if you still need an existing alias-node. If it hasn't been
         * accessed for a long time, you can probably delete it.
         * 
         * Defaults to true.
         * 
         * @var boolean
         */
        'enable_access_counter' => true,
    ]
];

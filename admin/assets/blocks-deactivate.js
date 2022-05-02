let mbfg_deactivated_blocks = mbfg_deactivate_blocks.deactivated_blocks
// If we are recieving an object, let's convert it into an array.
if ( mbfg_deactivated_blocks.length ) {
	if ( typeof wp.blocks.unregisterBlockType !== "undefined" ) {
		for( block_index in mbfg_deactivated_blocks ) {
			wp.blocks.unregisterBlockType( mbfg_deactivated_blocks[block_index] );
		}
	}

}

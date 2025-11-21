<?php
$sellers = [
    's','s','s','s','s','s',
    's','s','s','s','s','s'
];
?>

<div class="top-sellers container mx-auto py-6">
  

  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
    <?php foreach($sellers as $name): ?>
      <div class="flex flex-col items-center">
        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
          
        </div>
        <div class="mt-3 text-sm text-gray-800 text-center"><?= htmlspecialchars($name) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

$testScope = {};

jQuery(function ($) {
    var bestFont = null;
    var $scope = angular.element(document.getElementById("designer-controller")).scope();

    function maybeFindBestFont() {
        if (bestFont !== null) return;
        var fontStatictics = {};
        _.each($scope.stages, function (stage) {
            stage.canvas.forEachObject(function (obj) {
                if (obj.type === 'i-text' || obj.type === 'textbox' || obj.type === 'text') {
                    var fontName = obj.get('fontFamily');
                    if (!fontStatictics.hasOwnProperty(fontName)) {
                        fontStatictics[fontName] = 1;
                    } else {
                        fontStatictics[fontName] = fontStatictics[fontName] + 1;
                    }
                }
            });
        });

        if (Object.keys(fontStatictics).length === 0) return;

        var bestMatch = Object.keys(fontStatictics).reduce(function (a, b) {
            return fontStatictics[a] > fontStatictics[b] ? a : b
        });
        if (bestMatch) {
            bestFont = bestMatch;
        }

    }

    var addText = function (content, textObj) {
        var fontSize = textObj['fontSize'],
            fontName = textObj['fontFamily'];
        var state = $scope.stages[$scope.currentStage].states;

        textObj['ptFontSize'] = (textObj.fontSize / state.ratioConvertFont).toFixed(2);

        function add() {
            $scope.stages[$scope.currentStage]['canvas'].add(new fabric.Textbox(content, textObj));
        }

        var font = new FontFaceObserver(fontName);
        font.load(content).then(function () {
            fabric.util.clearFabricFontCache(fontName);
            var container = document.createElement('p'),
                scale = state.scaleRange[state.currentScaleIndex].ratio;
            container.innerHTML = content;
            container.style.cssText = [
                'position:absolute',
                'width:auto',
                'font-size: ' + fontSize + 'px',
                'font-family: ' + fontName,
                'left:-99999px',
                'white-space: pre'
            ].join(' !important;');
            document.body.appendChild(container);
            var textWidth = container.clientWidth + 2;
            document.body.removeChild(container);
            textObj.width = textWidth / scale;
            add();
        }, function () {
            console.log('Fail to load font: ' + fontName);
            add();
        });
    };

    $('.nbdt__quote-container').on('click', function () {
        var text = $(this).children('.nbdt__quote-content').text();
        maybeFindBestFont();
        addText(text, {
            'textAlign': 'center',
            'fontSize': 12 * $scope.stages[$scope.currentStage].states.ratioConvertFont,
            'fontFamily': bestFont ? bestFont : NBDESIGNCONFIG.default_font.alias,
            radius: 50,
            objectCaching: false
        });
    });

    $('.nbdt__checkbox-container input').on('change', function () {
        $(this).parent().parent().toggleClass('active', $(this).is(':checked'));
    });

    function validateCheckboxTab($tab) {
        var hasCheckboxes = $tab.find('.nbdt__checkbox-container input[type=checkbox]').length > 0;
        var checkedCount = $tab.find('.nbdt__checkbox-container input[type=checkbox]:checked').length;
        return hasCheckboxes === false || hasCheckboxes && checkedCount > 0;
    }

    $scope.maybeValidateTabs = function () {
        var isValid = true;
        $('.nbdesigner-tab').each(function () {
            if (!validateCheckboxTab($(this))) {
                var navId = '#' + $(this).attr('id').replace('tab-tab', 'nav-tab');
                setTimeout(function () {
                    angular.element(navId).triggerHandler('click');
                }, 10);
                var validationMessage = $(this).data('validationMessage');
                if (validationMessage) {
                    setTimeout(function () {
                        alert(validationMessage);
                    }, 300);
                }
                isValid = false;
                return false;
            }
        });
        return isValid;
    }

    $scope.serializeTabs = function () {
        var data = {};
        $('.nbdesigner-tab').each(function () {
            var tabId = $(this).data('tabId');

            var tmpArr = [];
            $(this).find('.nbdt__checkbox-container input[type=checkbox]:checked').each(function () {
                tmpArr.push($(this).val());
            });
            if (tmpArr.length > 0) {
                data[tabId] = Array.from(tmpArr);
            }
        });
        return data;
    }

    $testScope.validate = $scope.maybeValidateTabs;
    $testScope.serialize = $scope.serializeTabs;
});
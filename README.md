# シンプルなWYSIWYG
## 特徴
- リポジトリをクローンし、サーバへアップロードするだけで使用可能
- jQueryなどを使用しないため軽量
- 簡潔な実装で拡張性が高い
## 使用方法
- index.phpを任意のファイル名に変更してご利用ください。
- 内容を保存するためのsave_draft.phpはデータベース接続が必要です。保存はhtmlタグのまま保存されます。
- アップロードした画像は/image/uploads/ディレクトリに保存されます。ディレクトリ名の重複に注意。詳細はupload_image.phpを参照。
- style.cssは最低限の記述のみしています。
- 埋め込みで利用するにはiframeをご利用ください。

## 操作方法
- Ctrl+ZでUNDO
- Shift+Enterで改行
- Enterで段落を追加

### リンク挿入について
- 範囲選択してLINKを押すとURL入力することが可能

### 画像挿入について
- upload_image.phpにPOSTし、ファイルを保存後、保存したファイルをパスを参照して画像を表示します。
- php.iniの設定でファイル容量が大きいとアップロードできないことがあります。


## 推奨
- クエリパラメータなどを用いてデータベースのidなどと連携し、下書きを呼び出せるように実装可能です。
- execCommandは非推奨のため、Rangeなどを用いて実装しています。最低限の実装ですが、javascript内の関数を参考に欲しい機能を追加してください。

# 注意事項
- このファイルの利用により生じた結果のいかなる責任を負いません。
- 再配布、販売などを禁じます。
- 著作権は作成者に帰属します。

/* ****************************
 * Main Table class, handling Tables
 * This class derives from Database Abstract Class
 *
 * Author:          AMIN MATOLA
 * Date Modified:   17 Nov 2020
 */

package JavaMysql.databases;


import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.util.*;
import java.util.stream.Collectors;
import com.alibaba.fastjson.JSON;

public class Table extends Database{

    // Table columns, useful when table is new
    protected String table, columns  = "(";
    
    // Keeps executed query data as a list [of Maps]
    protected List __data;

    /* **********
     * Custom Constructor method of this class
     *
     * @param table - String - Table Name, New or Available
     * @param cols  - Map - Optional Map of Columns, when creating new table
     *
     * @return null
     */
    Table( String table, Map<String, String> ...cols) {
        //- Initialize Main Database Class before all others
        super.init();

        this.table      = table.length() > 1? table  : getConfig().TABLE;

        if( ! table.isEmpty() && cols.length > 0 && cols[0].size() > 0)
            createTable(table, cols[0]);
    }

    /* ***********
     * Creates Table from given parameters
     *
     * @Param table - String - Table Name
     * @Param cols  - Map of colums with datatype descriptions
     */
    void createTable( String table, Map<String, String> cols) {

        if(table.isEmpty()){
            table = this.table;
        }

        if(table.isEmpty() || cols.size() < 1){
            return;
        }

        cols.forEach((k,v) -> {
            columns += f("%s %s,", k, v);
        });

        columns   = columns.substring(0, columns.length() - 1 ) + ")";

        String query  = f("CREATE TABLE IF NOT EXISTS %s %s;", table, columns);

        this.run(query);
    }

    /* *
     * And now we wanna change table name
     *
     * @param what - String - The new name of the table  
     * */
    public void rename( String what ){

       this.run(f("RENAME TABLE %s TO %s", this.table, what));
       this.table = what;
    }

    /* *
     * What if we want to delete this table
     *
     * @param table - String - Name of the table to be deleted
     * */
    public boolean delete(String ...table){
        
        String _tb        = table.length > 0 ? table[0] : this.table;

        if( ! _tb.isEmpty() ){
            this.clear(_tb);
            this.run(f("DROP TABLE IF EXISTS %s", _tb));
            return true;
        }
        return false;
    }

    /* *
     * Read the results of this table after executing the query
     *
     * @param source - Resultset - The Query Result Object
     * @param data   - Additional Data
     *
     * @return List - List of Map(s) of queried data
     * */
    public List readResults(ResultSet source){
            List<Map> results = new ArrayList<>();


            try{
                ResultSetMetaData md = source.getMetaData();

                while(source.next()){
                    Map<String, Object> m = new HashMap();
                    for (int i = 1; i <= md.getColumnCount(); i++){
                        try {
                            m.put(md.getColumnName(i), source.getInt(i));
                        } catch (Exception e){
                            m.put(md.getColumnName(i), source.getString(md.getColumnLabel(i)));
                        }

                    };
                    results.add(m);
                }
            } catch (Exception e){
                System.out.println(e);
            }

            __data   = results;

            return __data;
    }

    /* *
     * Get all the data in this table
     *
     * @return List of Data Read from This table in the database.
     * */
    public List getData(String ...query){
        return readResults(this.run(f("SELECT * FROM %s", this.table), true));
    }
    
    /* *
     * Get JSON of this query
     *
     * @param query - The query to be executed
     * @return JSON String of the queried data
     * */
    Object getJson(String ...query) {
        if(query.length > 0)
            return JSON.toJSONString(getData(query));
        return getData();
    }

    /* *
     * Insert data into the table
     *
     * @param table - String - The table of which to insert data to
     * @param data  - Map    - The Map of key - value data to be inserted
     * */
    public void insert(String table, Map<String, Object> data){

        if(table.isEmpty()){
            table  = !this.table.isEmpty()? this.table : getConfig().TABLE;
        }

        if(table.isEmpty() || data.size() < 0 || data instanceof Map != true){
            return;
        }

        String q         = f("INSERT INTO %s(", table);

        String cols      = String.join(",", data.keySet());
        String vals      = String.join(",", quote(new ArrayList(data.values())));


        String query     = f("%s %s ) VALUES( %s );", q, cols, vals);
        run(query);

    }

    /* *
     * Quote the provided data into strings
     *
     * @param data - List of data to be quoted
     *
     * @return List of data quoted ready to be inserted into database
     * */
    public List quote(List data) {
        return (List) data.stream().map(
                (i) -> f("'%s'", String.valueOf(i))
        ).collect(Collectors.toList());
    }

    /* *
     * Build Test Parameters, to check for where/what of the rows to pick
     *
     * @param item - Map of Key - Value pairs to check from the database
     *             - If the Map is empty, then "where" clause will have 1 = 1
     * @return formatted string to fit "where x = y" clause
     * */
    public String buildTest(Map<String, Object> item){
        String result = "";

        if(item.isEmpty())
            result     = "1 = 1";

        for(Map.Entry<String, Object> iterator : item.entrySet()) {
            if (!result.isEmpty() && !result.isBlank())
                result = f("%s and %s='%s'", result, iterator.getKey(), (String) iterator.getValue());
            else
                result = f("%s='%s'", iterator.getKey(), iterator.getValue());
        }
        return result;
    }

    /* *
     * Get single row as array
     *
     * @param test - The key - value pair map to test where to take single row, sent to buildTest
     *
     * @return The single proposed item
     * */
    public Map getOne(Map<String, Object> ...test){


        Map<String, Object>  term   =   test.length > 0? test[0] : new HashMap<>();

        List l = readResults(
                            this.run(
                                    f("SELECT * FROM %s WHERE %s;",
                                            this.table, buildTest(term)),
                                    true)
                    );

        if( !l.isEmpty() )
            return (Map) l.get(0);


        return term;
    }

    /* *
     * What if we want to add a column
     *
     * @param name         - String - Name of the column to be added
     * @param description  - String - The description of the column, i.e INT, TEXT etc.
     * @param after        - String, Optional - The name of the column this column should come after, default - to the end
     * */
    public void addColumn(String name, String description, String ...after){

        if( name.isEmpty() || name.isBlank() )
            return;
        String pos = after.length < 1? this.lastColumn() : after[0];

        String q      = f(
                    "ALTER TABLE %s ADD COLUMN %s %s %s;",
                            this.table,
                            name,
                            description,
                            pos.length() > 1 ? "after " + pos : ""
                        );
        this.run(q);
    }

    /* *
     * Get name of the last colum of this table
     * 
     * @return name - String - Name of the last column of this table
     * */
    public String lastColumn() {
        String q                = f("SELECT * from %s", this.table);
        ResultSet r             = this.run(q, true);

        try {
            ResultSetMetaData md = r.getMetaData();
            return md.getColumnLabel(md.getColumnCount());
        }catch(Exception e){
            print(e);
        }

        return "";
    }

    /* *
     * Get the ID of the last column of this table
     *
     * @return id - int - The ID of the last column
     * */
    public int lastColumnId() {
        String q                = f("SELECT * from %s", this.table);
        ResultSet r             = this.run(q, true);

        try {
            return r.getMetaData().getColumnCount();
        } catch( Exception e) {}
        return 0;
    }

    /* *
     * Rename column
     *
     * @param from - String - The Old name of the column to be renamed
     * @param to   - String - The new name of which to rename this column with
     * @param type - String - The type of the column, example "INT", or "INT NOT NULL" etc.
     * */
    public void renameColumn( String from, String to, String type){
        this.run(f("ALTER TABLE %s CHANGE %s %s %s", this.table, from, to, type));
    }

    /* *
     * Remove an existing column
     *
     * @param name - String - Name of the column to be removed
     * */
    public void removeColumn(String name){
        this.run(f("ALTER TABLE %s DROP COLUMN %s", this.table, name));
    }

    /* *
     * And we want to clear the table
     * */
    public void clear(String ...name){
        if(name.length > 0)
            this.table = name[0];
        this.run(f("DELETE FROM %s", this.table));
    }
}

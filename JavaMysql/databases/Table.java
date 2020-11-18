package JavaMysql.databases;


import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.util.*;
import java.util.stream.Collectors;

public class Table extends Database{

    public String table;
    String columns  = "(";
    public List __data;

    Table( String table, Map<String, String> ...cols) {

        super.init();

        this.table      = table.length() > 1? table  : getConfig().TABLE;

        if( ! table.isEmpty() && cols.length > 0 && cols[0].size() > 0)
            createTable(table, cols[0]);
    }

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

        columns   = columns.substring(0, columns.length() -1 ) + ")";

        String query  = f("CREATE TABLE IF NOT EXISTS %s %s;", table, columns);

        this.run(query);
    }

    /* *
     * And now we wanna change table name
     * */
    public void rename( String what ){

       this.run(f("RENAME TABLE %s TO %s", this.table, what));
       this.table = what;
    }

    /* *
     * What if we want to delete this table
     * */
    public boolean delete(String table){
        if(table.isEmpty()){
            table = this.table;
        }

        if( table.isEmpty() ){
            this.run(f("DROP TABLE IF EXISTS %s", table));
            return true;
        }
        return false;
    }

    public List readResults(ResultSet source, Object ...data){
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
     * */
    public List getData(){
        return readResults(this.run(f("SELECT * FROM %s", this.table), true));
    }

    /* *
     * Insert data into the table
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
        String vals      = String.join(",", quoteAll(new ArrayList(data.values())));


        String query     = f("%s %s ) VALUES( %s );", q, cols, vals);
        run(query);

    }

    public List quoteAll(List data) {
        return (List) data.stream().map(
                (i) -> f("'%s'", String.valueOf(i))
        ).collect(Collectors.toList());
    }

    /* *
     * And we want to clear the table
     * */
    public void clear(){
        this.run(f("DELETE FROM %s", this.table));
    }
}

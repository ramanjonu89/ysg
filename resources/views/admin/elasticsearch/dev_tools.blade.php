@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 h6">Elasticsearch Dev Tools</h3>
                <div class="float-right">
                    <button id="get-cluster-info" class="btn btn-info btn-sm">Thông tin Cluster</button>
                    <button id="clear-console" class="btn btn-secondary btn-sm">Xóa console</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Cluster Status -->
                <div id="cluster-status" class="mb-3" style="display: none;">
                    <div class="alert alert-info">
                        <strong>Cluster Status:</strong> <span id="cluster-status-text"></span>
                        <button type="button" class="close" onclick="$('#cluster-status').hide();">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>

                <!-- Main Interface -->
                <div class="row">
                    <!-- Request Panel -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Request</h6>
                                <div class="float-right">
                                    <button id="run-query" class="btn btn-success btn-sm">
                                        <i class="fas fa-play"></i> Chạy
                                    </button>
                                    <button id="format-query" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-code"></i> Format
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <textarea id="query-editor" class="form-control" rows="20" placeholder="# Nhập Elasticsearch query của bạn ở đây
# Ví dụ:

GET _cluster/health

GET _cat/indices

GET products/_search
{
  &quot;query&quot;: {
    &quot;match_all&quot;: {}
  }
}

POST products/_search
{
  &quot;query&quot;: {
    &quot;match&quot;: {
      &quot;name&quot;: &quot;laptop&quot;
    }
  },
  &quot;size&quot;: 10
}"></textarea>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-outline-primary btn-sm btn-block mb-2" onclick="insertQuery('GET _cluster/health')">Cluster Health</button>
                                        <button class="btn btn-outline-primary btn-sm btn-block mb-2" onclick="insertQuery('GET _cat/indices')">List Indices</button>
                                        <button class="btn btn-outline-primary btn-sm btn-block mb-2" onclick="insertQuery('GET _cat/nodes')">List Nodes</button>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-outline-secondary btn-sm btn-block mb-2" onclick="insertQuery('GET products/_mapping')">Products Mapping</button>
                                        <button class="btn btn-outline-secondary btn-sm btn-block mb-2" onclick="insertQuery('GET products/_search\n{\n  \"query\": {\n    \"match_all\": {}\n  },\n  \"size\": 10\n}')">Search Products</button>
                                        <button class="btn btn-outline-secondary btn-sm btn-block mb-2" onclick="insertQuery('GET products/_count')">Count Products</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Panel -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Response</h6>
                                <div class="float-right">
                                    <span id="execution-time" class="text-muted small"></span>
                                    <button id="copy-response" class="btn btn-outline-secondary btn-sm ml-2">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <pre id="response-panel" class="bg-light p-3 m-0" style="height: 400px; overflow-y: auto; font-size: 12px;">
Kết quả sẽ hiển thị ở đây...
                                </pre>
                            </div>
                        </div>

                        <!-- Response Stats -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Response Stats</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted">Status</small>
                                            <div id="response-status" class="h6">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted">Time</small>
                                            <div id="response-time" class="h6">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted">Size</small>
                                            <div id="response-size" class="h6">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#query-editor {
    border: none;
    resize: none;
    font-family: 'Monaco', 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
}

#response-panel {
    font-family: 'Monaco', 'Courier New', monospace;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.card-header h6 {
    line-height: 1.8;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.775rem;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<script>
$(document).ready(function() {
    
    // Run Query
    $('#run-query').click(function() {
        const query = $('#query-editor').val().trim();
        
        if (!query) {
            alert('Vui lòng nhập query!');
            return;
        }

        runElasticsearchQuery(query);
    });

    // Get Cluster Info
    $('#get-cluster-info').click(function() {
        getClusterInfo();
    });

    // Clear Console
    $('#clear-console').click(function() {
        $('#query-editor').val('');
        $('#response-panel').text('Kết quả sẽ hiển thị ở đây...');
        resetResponseStats();
    });

    // Format Query
    $('#format-query').click(function() {
        const query = $('#query-editor').val();
        try {
            // Simple JSON formatting for queries
            const lines = query.split('\n');
            let formatted = '';
            for (let line of lines) {
                line = line.trim();
                if (line.startsWith('{') || line.startsWith('[')) {
                    const parsed = JSON.parse(line);
                    formatted += JSON.stringify(parsed, null, 2) + '\n';
                } else {
                    formatted += line + '\n';
                }
            }
            $('#query-editor').val(formatted);
        } catch (e) {
            console.log('Could not format query:', e);
        }
    });

    // Copy Response
    $('#copy-response').click(function() {
        const response = $('#response-panel').text();
        navigator.clipboard.writeText(response).then(function() {
            showToast('Đã copy response!', 'success');
        });
    });

    // Keyboard shortcuts
    $('#query-editor').keydown(function(e) {
        // Ctrl+Enter to run query
        if (e.ctrlKey && e.keyCode === 13) {
            e.preventDefault();
            $('#run-query').click();
        }
        
        // Tab for indentation
        if (e.keyCode === 9) {
            e.preventDefault();
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const value = this.value;
            this.value = value.substring(0, start) + '  ' + value.substring(end);
            this.selectionStart = this.selectionEnd = start + 2;
        }
    });
});

function runElasticsearchQuery(queryString) {
    showLoading(true);
    resetResponseStats();
    
    const startTime = performance.now();
    
    $.ajax({
        url: '{{ route("admin.elasticsearch.execute") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            query: queryString
        },
        success: function(response) {
            const endTime = performance.now();
            const executionTime = (endTime - startTime).toFixed(2);
            
            if (response.success) {
                displayResponse(response.response, 'success');
                updateResponseStats('200 OK', executionTime + 'ms', calculateResponseSize(response.response));
            } else {
                displayResponse(response.error, 'error');
                updateResponseStats('Error', executionTime + 'ms', '-');
            }
        },
        error: function(xhr) {
            const endTime = performance.now();
            const executionTime = (endTime - startTime).toFixed(2);
            
            let errorMessage = 'Lỗi kết nối Elasticsearch';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            
            displayResponse(errorMessage, 'error');
            updateResponseStats(xhr.status + ' ' + xhr.statusText, executionTime + 'ms', '-');
        },
        complete: function() {
            showLoading(false);
        }
    });
}

function getClusterInfo() {
    $.ajax({
        url: '{{ route("admin.elasticsearch.cluster-info") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const health = response.cluster_health;
                $('#cluster-status-text').html(
                    `<strong>${health.cluster_name}</strong> - Status: <span class="text-${health.status === 'green' ? 'success' : health.status === 'yellow' ? 'warning' : 'danger'}">${health.status}</span> - Nodes: ${health.number_of_nodes} - Indices: ${response.indices.length}`
                );
                $('#cluster-status').show();
                
                displayResponse(response, 'success');
            } else {
                displayResponse(response.error, 'error');
            }
        },
        error: function(xhr) {
            displayResponse('Không thể lấy thông tin cluster', 'error');
        }
    });
}

function displayResponse(data, type) {
    const responsePanel = $('#response-panel');
    
    if (type === 'error') {
        responsePanel.html(`<span class="text-danger"># ERROR\n${data}</span>`);
    } else {
        const formatted = JSON.stringify(data, null, 2);
        responsePanel.html(`<span class="text-success"># SUCCESS</span>\n${formatted}`);
    }
    
    responsePanel.scrollTop(0);
}

function updateResponseStats(status, time, size) {
    $('#response-status').text(status);
    $('#response-time').text(time);
    $('#response-size').text(size);
}

function resetResponseStats() {
    updateResponseStats('-', '-', '-');
}

function calculateResponseSize(data) {
    const jsonString = JSON.stringify(data);
    const bytes = new Blob([jsonString]).size;
    
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
    return Math.round(bytes / (1024 * 1024)) + ' MB';
}

function showLoading(show) {
    if (show) {
        $('#run-query').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang chạy...');
        $('.card').addClass('loading');
    } else {
        $('#run-query').prop('disabled', false).html('<i class="fas fa-play"></i> Chạy');
        $('.card').removeClass('loading');
    }
}

function insertQuery(query) {
    $('#query-editor').val(query);
}

function showToast(message, type) {
    // Simple toast implementation
    const toast = $(`<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>`);
    
    $('body').append(toast);
    
    setTimeout(function() {
        toast.alert('close');
    }, 3000);
}
</script>
@endsection 